<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id', 'date', 'overall_price','new_price', 'discount', 'discount_of_executing_agency_price', 'executing_agency_price'];

    public function materials()
    {
        return $this->hasMany(BillMaterial::class);
    }
}
