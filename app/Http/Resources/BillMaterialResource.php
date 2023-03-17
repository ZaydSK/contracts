<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BillMaterialResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $quantity = $this->quantity;
        if($this->stoppings != 0){
            $quantity -= $quantity * $this->stoppings/100; 
        }
        return [
            'id' => $this->id,
            'material_id' => $this->material_id,
            'bill_id' => $this->bill_id,
            'quantity' => $this->quantity,
            'stoppings' => $this->stoppings,
            'quantity_after_stoppings' => $quantity, 
            'price' => $this->price
        ];
    }
}
