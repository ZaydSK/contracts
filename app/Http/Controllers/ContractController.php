<?php

namespace App\Http\Controllers;

use App\Models\Contract;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContractRequest;

class ContractController extends Controller
{
    public function all(){
        return response(Contract::with('materials')->get());
    }

    public function one(Contract $contract){
        return $contract->with('materials');
    }

    public function add(StoreContractRequest $request){
        $payload = $request->safe()->except('materials');
        $contract = Contract::create($payload);
        return response($contract);
    }
}
