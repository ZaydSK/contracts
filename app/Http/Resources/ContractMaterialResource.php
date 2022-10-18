<?php

namespace App\Http\Resources;

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
        return [
            'id' => $this->id,
            'material_name' => $this->material_name,
            'contract_id' => $this->contract_id,
            'individual_price' => $this->individual_price,
            'overall_price' => $this->overall_price,
            'quantity' => $this->quantity,
            'unit' => $this->unit,
        ];
    }
}
