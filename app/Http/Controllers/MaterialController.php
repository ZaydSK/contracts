<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreContractMaterialRequest;
use App\Models\Contract;
use App\Models\ContractMaterial;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    
    public function all(Contract $contract){
        return response($contract->materials);
    }

    public function add(StoreContractMaterialRequest $request,Contract $contract){
        $payload = $request->validated();
        $payload['contract_id'] =  $contract->id;
        $payload['overall_price'] = $payload['quantity'] * $payload['individual_price'];
        $contractMaterial = ContractMaterial::create($payload);
        return response($contractMaterial);
    }
}
