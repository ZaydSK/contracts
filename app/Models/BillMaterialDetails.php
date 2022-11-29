<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillMaterialDetails extends Model
{
    use HasFactory;

    protected $fillable = [ 
        'material_amount_id',
        'quantity',
        'bill_material_id',
        'price'
    ];
}
