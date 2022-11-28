<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillRequest;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\StoreSubcontractRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\ContractMaterialResource;
use App\Http\Resources\ContractResource;
use App\Models\Bill;
use App\Models\BillMaterial;
use App\Models\ContractMaterial;
use App\Models\MaterialAmount;
use App\Models\Subcontract;
use Carbon\Carbon;

class ContractController extends Controller
{
    //TODO? ADD sub contract / each contract shouldn't have more than 10 subs
    //TODO? new price of all subs shouldn't exceed 25% of original new price
    public function all(){
        return ContractResource::collection(Contract::where('contract_id',null)->get());
    }

    public function one(Contract $contract){
        return new ContractResource($contract);
    }

    public function materials(Contract $contract){
        return ContractMaterialResource::collection($contract->materials);
    }

    public function add(StoreContractRequest $request){        
        $materials = $request->materials;
        $overall_price = 0;

        foreach($materials as $material){
            $overall_price += $material['overall_price'] ;
        }

        if($request['up_percent']!=0){
            $up_price = $overall_price + $overall_price*$request['up_percent']/100; 
        }
        else if($request['down_percent']!=0){
            $up_price = $overall_price*(100-$request['down_percent']/100); 
        }
        else{
            $up_price=$overall_price;
        }
        $virtual_finishing_date = Carbon::parse($request->starting_date)
                                    ->addDays($request->execution_period);
                                   
                                    //->toDateString();
        $request['up_price'] = $up_price;
        $request['price'] = $overall_price;
        $request['virtual_finishing_date'] = $virtual_finishing_date;
       // return $virtual_finishing_date->format('m-Y');
        //? Add up_price to request
        //? add virtual finishing date: starting date + executing period 
        $contract = Contract::create($request->except('materials'));
        $contract->materials()->createMany($materials);
        return new ContractResource($contract);
    }

    public function addSub(StoreSubcontractRequest $request,Contract $contract){ 
        $contract_id = $request->contract->id;       
        $contract_materials = $request->contract_materials;
        $other_materials = $request->other_materials;
        $overall_price = 0;

        if($contract->subs->count()>9){
            return response(['error'=>"لا يمكن إضافة أكثر من 10 عقود ملحقة"],400);
        }
        
         $res = $contract->materials()->createMany(array_map(function($material){
            return [
                'material_name' => $material['material_name'],
                'unit' => $material['unit'],
                'price' => $material['individual_price'],
                'number' => $material['number'],
                'contract_material' => 0
            ];
        },$other_materials));
        //return response($contract_materials_rows);
        $other_materials_amounts = [];
        $i=0;
        foreach($res as $material){
            $overall_price += $material->price;

            array_push($other_materials_amounts,[
                'material_id' => $material->id,
                'quantity' => $other_materials[$i]['quantity'],
                'not_used_quantity' =>$other_materials[$i]['quantity'],
                'individual_price' => $material->price,
                'overall_price' => $material->quantity* $material->price
            ]);
            $i++;
        }

        $contract_materials_amounts = [];
        foreach($contract_materials as $material){
            $material_price = ContractMaterial::where('id',$material['id'])->first();
            $material_amount = MaterialAmount::where('material_id',$material_price->id)->first();
            //return $material_amount;
            $overall_price += $material['quantity']* $material_price->price;

            array_push($contract_materials_amounts,[
                'material_id' => $material['id'],
                'quantity' => $material['quantity'],
                'not_used_quantity' => $material['quantity'],
                'individual_price' => $material_amount->individual_price,
                'overall_price' => $material['quantity']* $material_price->price
            ]);

        }
      //return $contract_materials_amounts;
        // if($request['up_percent']!=0){
        //     $up_price = $overall_price + $overall_price*$request['up_percent']/100; 
        // }
        // else if($request['down_percent']!=0){
        //     $up_price = $overall_price*(100-$request['down_percent']/100); 
        // }
        // else{
        //     $up_price=$overall_price;
        // }

        // $subs_prices = Subcontract::where('contract_id',$contract_id)->sum('up_price');
        // if($contract->up_price/4 < $subs_prices + $up_price){
        //     return response(['error'=>"لا يمكن أن تكون قيمة العقود الفرعية أكبر من ربع العقد الأساسي"],400);
        // }

        $request['contract_id'] = $contract_id;
        //$request['up_price'] = $up_price;
        $request['price'] = $overall_price;
        $subcontract = Subcontract::create($request->except('contract_materials','other_materials'));
        $subcontract->materialAmounts()->createMany($other_materials_amounts);
        $subcontract->materialAmounts()->createMany($contract_materials_amounts);
        return new ContractResource($contract);
    }

    public function search(Request $request){
        $contracts = Contract::query();
        if($request->number) $contracts->where('number','like','%'.$request->number.'%');
        if($request->branch) $contracts->where('branch','like','%'.$request->branch.'%');
        if($request->executing_agency) $contracts->where('executing_agency','like','%'.$request->executing_agency.'%');

        return ContractResource::collection($contracts->get());
    }

    public function addBill(Contract $contract,StoreBillRequest $request){
        $canAddBill = true;
        //TODO? make date format without day
        //TODO? add discount to each bill
        //TODO? add sub contract price (each sub on its own)
        //TODO? executing agency price: new price - discounts + sub contract price
        //TODO? discount of executing agency price: executing agency price - stopping_percent
        // TODO? executing agency price after discount
        $bill_price = 0;
        $materials = [];
        foreach($request->materials as $material){
            $old_bills = Bill::where('contract_id',$contract->id)->pluck('id');
            $used_quantity = BillMaterial::whereIn('bill_id',$old_bills)->sum('quantity');
            $all_quantity = ContractMaterial::where('id',$material['material_id'])->where('contract_id',$contract->id)->sum('quantity');
            if($all_quantity - $used_quantity < $material['quantity']){
                $left = $all_quantity - $used_quantity ;
                return response(["error"=>"لا يوجد كمية كافية من المادة ". $material['material_id']. "،المتبقي هو ". $left]);
            }
            
            $material_price = ContractMaterial::where('id',$material['material_id'])->where('contract_id',$contract->id)->first()->individual_price;
            $bill_price += $material['quantity'] * $material_price;
            $material['price'] = $material_price;
            array_push($materials, $material);
        }
        $up_price=0;
        if($contract['up_percent']!=0){
            $up_price = $bill_price + $bill_price*$contract['up_percent']/100; 
        }
        else if($contract['down_percent']!=0){
            $up_price = $bill_price*(100-$contract['down_percent']/100); 
        }
        else{
            $up_price=$bill_price;
        }
    
        $executing_agency_price =  $up_price - $request->discount + $contract->subs->sum('up_price');
        
        $bill = Bill::create([
            'contract_id' => $contract->id,
            'date' => $request->date,
            'discount' => $request->discount,
            'price' => $bill_price,
            'up_price' => $up_price,
            'executing_agency_price' => $executing_agency_price,
            'discount_of_executing_agency_price' => $executing_agency_price * $contract->stoppings_percent /100
        ]);

       
        $bill->materials()->createMany($materials);
        $bill->subs = $contract->subs;
        return new BillResource($bill);
    }

    public function oneBill(Contract $contract,Bill $bill){
        if($bill->contract_id!=$contract->id){
            return response("لا يوجد كشف يحمل المعرف المطلوب ضمن مشوفات العقد الحالي",400);
        }
        return new BillResource($bill);
    }

    public function allBills(Contract $contract){
        return BillResource::collection(Bill::where('contract_id',$contract->id)->get());
    }
}
