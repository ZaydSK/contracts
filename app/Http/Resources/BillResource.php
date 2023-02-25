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
        $price = (array_sum(array_column($this->subs(),'price')) +
        $this->getContractMaterialsPrice() );
        
        $price_after_stoppings= $price - $price*$this->contract->stoppings_percent/100;
        $price_after_discount = $price_after_stoppings - $this->discount;
        return [
            'id'=> $this->id,
            'number' =>$this->number,
            'contract_id' => $this->contract_id,
            'date' => substr($this->date,0,7),
            'price' => $this->price,
            'up_price' => $this->up_price,
            'completion_percent' => $this->completion_percent,
            'accumulated_completion_percent' => $this->accumulated_completion_percent,
            'contract_materials_price' => $this->getContractMaterialsPrice(),
            'subs' => $this->subs(),
            'stoppings' => $this->contract->stoppings_percent,
            'stoppings_value' =>  $price*$this->contract->stoppings_percent/100,
            'price_after_stoppings' => $price_after_stoppings,
            'discount' => $this->discount,
            'price_after_stoppings_and_discount' => $price_after_discount ,
            'materials' => BillMaterialResource::collection($this->materials),
            
        ];
    }
}
