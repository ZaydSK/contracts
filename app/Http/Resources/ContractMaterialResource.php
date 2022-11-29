<?php

namespace App\Http\Resources;

use App\Models\ContractMaterial;
use Illuminate\Http\Resources\Json\JsonResource;

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
        $material =  ContractMaterial::where('id',$this->material_id)->first();
        
        return [
            'id' => $material->id,
            'number' => $material->number,
            'material_name' => $this->when($material->parentable_type == "App\\Models\\Subcontract
            " && !$material->contract_material,$material->material_name . "(" .$this->parentable->number .")",
                 $material->material_name),
            'contract_id' => $material->contract_id,
            'individual_price' => $this->individual_price,
            'overall_price' => $this->overall_price,
            'quantity' => $this->quantity,
            'not_used_quantity' => $this->not_used_quantity,
            'unit' => $material->unit,
        ];
    }
}
