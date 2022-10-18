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
        'individual_price',
        'overall_price',
        'quantity',
        'unit',
    ];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }
}
