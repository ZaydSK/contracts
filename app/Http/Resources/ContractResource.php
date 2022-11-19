<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
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
            'name'  => $this->name,
            'date'  => $this->date,
            'branch'  => $this->branch,
            'content'  => $this->content,
            'up_percent'  => $this->up_percent,
            'down_percent'  => $this->down_percent,
            'stoppings_percent'  => $this->stoppings_percent,
            'number'  => $this->number,
            'price'  => $this->price,
            'up_price'  => $this->up_price,
            'starting_date'  => $this->starting_date,
            'finishing_date'  => $this->finishing_date,
            'virtual_finishing_date'  => $this->virtual_finishing_date,
            'execution_period'  => $this->execution_period,
            'executing_agency'  => $this->executing_agency,
            'watching_agency'  => $this->watching_agency,
            'materials' =>  ContractMaterialResource::collection($this->materials),
            'parent_id' => $this->when($request->parent_id,$this->parent_id)
        ];
    }
}
