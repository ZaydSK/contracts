<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    use HasFactory;

    protected $fillable = ['contract_id','date','overall_price'];

    public function materials(){
        return $this->hasMany(BillMaterial::class);
    }
}
