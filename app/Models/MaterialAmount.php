<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialAmount extends Model
{
    use HasFactory;

    protected $fillable = [
        'quantity',
        'not_used_quantity',
        'individual_price',
        'overall_price',
        'material_id'
    ];

    public function parentable(){
        return $this->morphTo();
    }
}
