<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractMaterial extends Model
{
    use HasFactory;
    protected $fillable = [
        'material_name',
        'contract_id',
        'price',
        'contract_material',
        'unit',
        'number'
    ];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }

    public function increase(){
        return $this->hasOne(Increase::class);
    }
}
