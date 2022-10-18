<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContractRequest;
use App\Http\Resources\ContractMaterialResource;
use App\Http\Resources\ContractResource;
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

    public function add(StoreContractRequest $request){
        $materials = $request->materials;
        $overall_price = 0;
       
        foreach($materials as $material){
            $overall_price += $material['overall_price'] ;
        }
        if($overall_price>= $request->price){
            return response(['error'=>"سعر المواد الكلي أكبر قيمة العقد"]);
        }
        $contract = Contract::create($request->except('materials'));
        $contract->materials()->createMany($materials);
        return new ContractResource($contract);
    }
}
