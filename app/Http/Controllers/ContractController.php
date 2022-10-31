<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillRequest;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContractRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\ContractMaterialResource;
use App\Http\Resources\ContractResource;
use App\Models\Bill;
use App\Models\BillMaterial;
use App\Models\ContractMaterial;

class ContractController extends Controller
{
    public function all(){
        return ContractResource::collection(Contract::all());
    }

    public function one(Contract $contract){
        return new ContractResource($contract);
    }

    public function materials(Contract $contract){
        return ContractMaterialResource::collection($contract->materials);
    }

    public function add(StoreContractRequest $request){        $materials = $request->materials;
        $overall_price = 0;

        foreach($materials as $material){
            $overall_price += $material['overall_price'] ;
        }
        if($overall_price>= $request->price){
            return response(['error'=>"سعر المواد الكلي أكبر قيمة العقد"],400);
        }
        $contract = Contract::create($request->except('materials'));
        $contract->materials()->createMany($materials);
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
        
        $bill = Bill::create([
            'contract_id' => $contract->id,
            'date' => $request->date,
            'overall_price' => $bill_price
        ]);

        $bill->materials()->createMany($materials);
        return new BillResource($bill);
    }

    public function oneBill(Contract $contract,Bill $bill){
        return new BillResource($bill);
    }

    public function allBills(){
        return BillResource::collection(Bill::all());
    }
}
