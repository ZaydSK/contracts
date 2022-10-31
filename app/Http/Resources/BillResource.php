<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillResource extends JsonResource
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
            'id'=> $this->id,
            'contract_id' => $this->contract_id,
            'date' => $this->date,
            'overall_price' => $this->overall_price,
            'materials' => BillMaterialResource::collection($this->materials)
        ];
    }
}
