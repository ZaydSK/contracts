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
        'executing_agency_price'
    ];

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
                'used_quantity' => $sub['quantity']
            ]);
        
        };
       
        return $infos;
    }
}
