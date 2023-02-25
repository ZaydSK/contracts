<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'contract_id',
        'date',
        'price',
        'up_price',
        'discount', 
        'discount_of_executing_agency_price', 
        'executing_agency_price',
        'completion_percent',
        'accumulated_completion_percent',
    ];

    

    public function getContractMaterialsPrice(){
        $subs = BillMaterial::where('bill_id',$this->id)->pluck('id');
        $subs_details = BillMaterialDetails::whereIn('bill_material_id',$subs)->get()->toArray();
        $price = 0;
        foreach($subs_details as $sub) {
            $material = MaterialAmount::where('id',$sub['material_amount_id'])->where(function($query){
                $query->where('parentable_type','App\Models\Contract')
                ->orWhere('parentable_type','App\Models\Increase');
            })->first();
            if(!$material){continue;}
            $up_price = $sub['price'];
            if($this->contract->up_percent != 0){
                $up_price += $sub['price'] * $this->contract->up_percent/100;
            } else {
                $up_price += $sub['price'] * $this->contract->down_percent/100;
            }
            $price += $up_price ; 
        };
       
        return $price;
    }

    public function materials()
    {
        return $this->hasMany(BillMaterial::class);
    }

    public function contract(){
        return $this->belongsTo(Contract::class);
    }

    public function subs(){
        $subs = BillMaterial::where('bill_id',$this->id)->pluck('id');
        $subs_details = BillMaterialDetails::whereIn('bill_material_id',$subs)->get()->toArray();
        $infos = [];
        foreach($subs_details as $sub) {
            $material = MaterialAmount::where([['id',$sub['material_amount_id']],['parentable_type','App\Models\Subcontract']])->first();
            if(!$material){continue;}
            $parent = $material->parentable;
            array_push($infos, [
                'sub_contract_number' => $parent['number'],
                'price' => $sub['price']
            ]);
        
        };
       
        return $infos;
    }
}
