<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBillRequest;
use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContractRequest;
use App\Http\Requests\StoreIncreaseRequest;
use App\Http\Requests\StoreSubcontractRequest;
use App\Http\Resources\BillResource;
use App\Http\Resources\ContractMaterialResource;
use App\Http\Resources\ContractResource;
use App\Models\Bill;
use App\Models\ContractMaterial;
use App\Models\Increase;
use App\Models\MaterialAmount;
use App\Models\Subcontract;
use Carbon\Carbon;

class ContractController extends Controller
{

    public function all()
    {
        return ContractResource::collection(Contract::all());
    }

    public function one(Contract $contract)
    {
        return new ContractResource($contract);
    }

    public function materials(Contract $contract)
    {
        return ContractMaterialResource::collection($contract->materials);
    }

    public function subs(Contract $contract)
    {
        return $contract->subs;
    }

    public function add(StoreContractRequest $request)
    {
        $materials = $request->materials;
        $overall_price = 0;

        foreach ($materials as $material) {
            $overall_price += $material['overall_price'];
        }

        if ($request['up_percent'] != 0) {
            $up_price = $overall_price + $overall_price * $request['up_percent'] / 100;
        } else if ($request['down_percent'] != 0) {
            $up_price = $overall_price * (100 - $request['down_percent'] / 100);
        } else {
            $up_price = $overall_price;
        }
        $virtual_finishing_date = Carbon::parse($request->starting_date)
            ->addDays($request->execution_period);
        $request['up_price'] = $up_price;
        $request['price'] = $overall_price;
        $request['virtual_finishing_date'] = $virtual_finishing_date;

        $contract = Contract::create($request->except('materials'));
        $materials = array_map(function ($material) use ($contract) {
            $res = $contract->materials()->create([
                'material_name' => $material['material_name'],
                'price' => $material['individual_price'],
                'number' => $material['number'],
                'unit' => $material['unit'],
            ]);
            $material['id'] = $res->id;
            return $material;
        }, $materials);

        $contract->materialAmounts()->createMany(array_map(function ($material) {
            return [
                'material_id' => $material['id'],
                'individual_price' => $material['individual_price'],
                'overall_price' => $material['overall_price'],
                'quantity' => $material['quantity'],
                'not_used_quantity' => $material['quantity'],
            ];
        }, $materials));
        return new ContractResource($contract);
    }

    public function addSub(StoreSubcontractRequest $request, Contract $contract)
    {
        $image_url = null ;
        if ($request->doc != null) {
            $imageName = time() . '.' . $request->doc->extension();
            $request->doc->move(storage_path('app/public/subs'), $imageName);
            $image_url = '/storage/subs/' . $imageName;
        }

        $contract_id = $request->contract->id;
        $contract_materials = $request->contract_materials;
        $other_materials = $request->other_materials;
        $overall_price = 0;

        if ($contract->subs->count() > 9) {
            return response(['error' => "لا يمكن إضافة أكثر من 10 عقود ملحقة"], 400);
        }

        if ($other_materials) {
            $other_materials = array_map(function ($material) use ($contract, $overall_price) {
                $res = $contract->materials()->create([
                    'material_name' => $material['material_name'],
                    'price' => $material['individual_price'],
                    'number' => $material['number'],
                    'unit' => $material['unit'],
                    'contract_material' => 0
                ]);
                $material['material_id'] = $res->id;
                $material['not_used_quantity'] = $material['quantity'];


                return $material;
            }, $other_materials);

            foreach ($other_materials as $material) {
                $overall_price += $material['overall_price'];
            }
        }


        if ($contract_materials) {
            $contract_materials_amounts = [];
            foreach ($contract_materials as $material) {
                $material_amount = MaterialAmount::where('material_id', $material['id'])->first();
                $overall_price += $material['quantity'] * $material_amount->individual_price;

                array_push($contract_materials_amounts, [
                    'material_id' => $material['id'],
                    'quantity' => $material['quantity'],
                    'not_used_quantity' => $material['quantity'],
                    'individual_price' => $material_amount->individual_price,
                    'overall_price' => $material['quantity'] * $material_amount->individual_price
                ]);
            }
        }

        $request['contract_id'] = $contract_id;
        $request['price'] = $overall_price;
        $subcontract = $contract->subs()->create($request->except('contract_materials', 'other_materials','contract_id')+['doc_url'=>$image_url]);
        if ($other_materials) $subcontract->materialAmounts()->createMany($other_materials);
        if ($contract_materials) $subcontract->materialAmounts()->createMany($contract_materials_amounts);
        return new ContractResource(Contract::find($contract->id));
    }

    public function search(Request $request)
    {
        $contracts = Contract::query();
        if ($request->number) $contracts->where('number', 'like', '%' . $request->number . '%');
        if ($request->branch) $contracts->where('branch', 'like', '%' . $request->branch . '%');
        if ($request->executing_agency) $contracts->where('executing_agency', 'like', '%' . $request->executing_agency . '%');

        return ContractResource::collection($contracts->get());
    }

    public function addBill(Contract $contract, StoreBillRequest $request)
    {
        $bill_price = 0;
        $materials = [];
        foreach ($request->materials as $material) {
            $quantity = $material['quantity'];
            if($material['stoppings'] != 0){
                $quantity -= $quantity * $material['stoppings']/100.0; 
            }
            $not_used_quantity = MaterialAmount::where('material_id', $material['material_id'])->sum('not_used_quantity');
            if ($not_used_quantity < $quantity) {
                return response(["error" => "لا يوجد كمية كافية من المادة " . $material['material_id'] . "،المتبقي هو " . $not_used_quantity]);
            }

            $material_price = ContractMaterial::where('id', $material['material_id'])->where('contract_id', $contract->id)->first()->price;
            $bill_price += $quantity * $material_price;
            $material['price'] = $quantity * $material_price;
            array_push($materials, $material);
        }


        $up_price = 0;
        if ($contract['up_percent'] != 0) {
            $up_price = $bill_price + $bill_price * $contract['up_percent'] / 100;
        } else if ($contract['down_percent'] != 0) {
            $up_price = $bill_price * (100 - $contract['down_percent'] / 100);
        } else {
            $up_price = $bill_price;
        }

        $contractSubsPrice = $contract->subs()->sum('price');
        $contractIncreasesPrice = $contract->increases()->sum('price');
        $contractBillsPrice = $contract->bills()->sum('price');
        $completion_percent = 1.0*$bill_price / ($contractSubsPrice + $contractIncreasesPrice + $contract->price); 
        $accumulated_completion_percent = 1.0*($bill_price + $contractBillsPrice) / ($contractSubsPrice + $contractIncreasesPrice + $contract->price); 

        $executing_agency_price = 0;
        $number = Bill::where('contract_id', $contract->id)->count();
        $payload = [
            'number' => $number + 1,
            'contract_id' => $contract->id,
            'date' => $request->date,
            'discount' => $request->discount,
            'price' => $bill_price,
            'up_price' => $up_price,
            'executing_agency_price' => $executing_agency_price,
            'discount_of_executing_agency_price' => $executing_agency_price * $contract->stoppings_percent / 100,
            'completion_percent' => $completion_percent,
            'accumulated_completion_percent' => $accumulated_completion_percent
        ];
       // return $payload;
        $bill = Bill::create($payload);

        foreach ($materials as $material) {
            $quantity = $material['quantity'];
                if($material['stoppings'] != 0){
                    $quantity -= $quantity * $material['stoppings']/100; 
                }
            $bill_material = $bill->materials()->create($material + ['stoppings'=>$material['stoppings']]);

            $ids = Subcontract::where('contract_id', $contract->id)->pluck('id')->toArray();
            $ids2 = Increase::where('contract_id', $contract->id)->pluck('id')->toArray();
            $allIds = array_merge($ids, $ids2);
            $materialAmounts = MaterialAmount::where('material_id', $material['material_id'])
                ->where(function ($query) use ($contract, $allIds) {
                    $query->where([
                        ['parentable_type', "App\\Models\\Contract"],
                        ['parentable_id', $contract->id]
                    ])->orWhere(function ($query) use ($allIds) {
                        $query->whereIn('parentable_id', $allIds)
                            ->where('parentable_type', "App\\Models\\Subcontract");
                    })->orWhere(function ($query) use ($allIds) {
                        $query->whereIn('parentable_id', $allIds)
                            ->where('parentable_type', "App\\Models\\Increase");
                    });
                })->get();

            foreach ($materialAmounts as $amount) {
                
                if (($material['material_id'] == $amount->material_id) && ($amount->not_used_quantity > 0)) {

                    if ($quantity <= $amount->not_used_quantity) {
                        $bill_material->details()->create([
                            'material_amount_id' => $amount['id'],
                            'price' => $amount['individual_price'] * $quantity,
                            'quantity' => $quantity
                        ]);
                        $amount->not_used_quantity -= $quantity;

                        $amount->save();
                        break;
                    } else {
                        $bill_material->details()->create([
                            'material_amount_id' => $amount['id'],
                            'price' => $amount['individual_price'] * $amount->not_used_quantity,
                            'quantity' =>  $amount->not_used_quantity
                        ]);
                        $quantity -= $amount->not_used_quantity;
                        $amount->not_used_quantity = 0;
                        $amount->save();
                    }
                }
            }
        }


        $bill->subs = $contract->subs;
        return new BillResource($bill);
    }

    public function oneBill(Contract $contract, Bill $bill)
    {
        if ($bill->contract_id != $contract->id) {
            return response("لا يوجد كشف يحمل المعرف المطلوب ضمن مشوفات العقد الحالي", 400);
        }
        return new BillResource($bill);
    }

    public function allBills(Contract $contract)
    {
        return BillResource::collection(Bill::where('contract_id', $contract->id)->get());
    }

    public function addIncrease(Contract $contract, StoreIncreaseRequest $request)
    {
        $image_url = null ;
        if ($request->doc != null) {
            $imageName = time() . '.' . $request->doc->extension();
            $request->doc->move(storage_path('app/public/increases'), $imageName);
            $image_url = '/storage/increases/' . $imageName;
        }
        $increase = Increase::where('contract_id', $contract->id)->pluck('materials')->toArray();
        $filtered = [];
        foreach ($increase as $item) {
            foreach ($item as $itm) {
                array_push($filtered, $itm['id']);
            }
        }
        $materials_price = 0;
        $materials = [];
        foreach ($request->materials as $material) {

            foreach ($filtered as $itm) {
                if ($itm == $material['id']) {
                    return response(['error' => 'لا يمكنك إضافة نسبة لمادة تمت اضافة نسبة لها مسبقاً'], 400);
                }
            }

            $cMaterial = ContractMaterial::where('id', $material['id'])->first();
            $original_quantity = MaterialAmount::where('material_id', $material['id'])->first()['quantity'];
            $increase_quantity = floor($original_quantity * $material['percent'] / 100);
            $increase_price = $cMaterial['price'];
            if ($contract->up_percent != 0) {
                $increase_price += $cMaterial['price'] * $contract->up_percent / 100;
            } else {
                $increase_price += $cMaterial['price'] * $contract->down_percent / 100;
            }
            $increase_price *= $increase_quantity;
            $materials_price += $increase_price;

            array_push($materials, [
                'id' => $material['id'],
                'percent' => $material['percent'],
                'price' => $increase_price,
                'quantity' => $increase_quantity,
            ]);
        };

        $all_increases = Increase::where('contract_id', $contract->id)->sum('price');
        if ($all_increases + $materials_price > $contract->up_price * 0.25) {
            return response('مجموع المواد يتجاوز قيمة ربع العقد', 400);
        }

        $increase = Increase::create([
            'contract_id' => $contract['id'],
            'number' => $request->number,
            'period' => $request->period,
            'date' => $request->date,
            'materials' => $materials,
            'price' => $materials_price,
            'doc_url' => $image_url
        ]);

        $increase->materialAmounts()->createMany(array_map(function ($material) {
            $cMaterial = ContractMaterial::where('id', $material['id'])->first();
            return [
                'material_id' => $material['id'],
                'individual_price' => $cMaterial['price'],
                'overall_price' => $material['price'],
                'quantity' => $material['quantity'],
                'not_used_quantity' => $material['quantity'],
            ];
        }, $materials));

        return response(['data' => $increase]);
    }

    public function increases(Contract $contract)
    {
        return response(Increase::where('contract_id', $contract['id'])->get());
    }
}
