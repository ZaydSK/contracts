<?php

namespace App\Http\Resources;

use App\Models\ContractMaterial;
use App\Models\MaterialAmount;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Log;

class ContractMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $material =  MaterialAmount::where('material_id',$this->id)->first();
        $nuq =  MaterialAmount::where([['material_id',$this->id],['parentable_type','App\Models\Increase']])->sum('not_used_quantity');
        $q =  MaterialAmount::where([['material_id',$this->id],['parentable_type','App\Models\Increase']])->sum('quantity');
        
        return [
            'id' => $this->id,
            'number' => $this->number,
            'material_name' => 
            $this->when($this->parentable_type == "App\\Models\\Subcontract
            " && !$this->contract_material,$material->material_name . "(" .$material->parentable->number .")",
                 $this->material_name),
            'contract_id' => $this->contract_id,
            'individual_price' => $material->individual_price,
            'overall_price' => $material->overall_price,
            'quantity' => $material->quantity,
            'all_quantity' => $material->quantity + $q,
            'not_used_quantity' => $material->not_used_quantity,
            'increase' => $q - $nuq,
            'unit' => $this->unit,
        ];
    }
}
