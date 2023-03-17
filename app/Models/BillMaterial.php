<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillMaterial extends Model
{
    use HasFactory;
    
    protected $fillable = ['material_id','bill_id','quantity','price','stoppings'];

    public function bill(){
        return $this->belongsTo(Bill::class);
    }

    public function details(){
        return $this->hasMany(BillMaterialDetails::class);
    }
}
