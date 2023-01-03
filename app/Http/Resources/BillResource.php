<?php

namespace App\Http\Resources;

use App\Models\Contract;
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
            'date' => substr($this->date,0,7),
            'price' => $this->price,
            'up_price' => $this->up_price,
            'discount' => $this->discount,
            'executing_agency_price' => $this->executing_agency_price,
            'discount_of_executing_agency_price' => $this->discount_of_executing_agency_price,
            'executing_agency_price_after_discount' => $this->executing_agency_price - $this->discount_of_executing_agency_price,
            'subs' => $this->subs(),
            'materials' => BillMaterialResource::collection($this->materials)
        ];
    }
}
