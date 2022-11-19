<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'date',
        'branch',
        'content',
        'up_percent',
        'down_percent',
        'stoppings_percent',
        'number',
        'price',
        'up_price',
        'starting_date',
        'finishing_date',
        'virtual_finishing_date',
        'execution_period',
        'executing_agency',
        'watching_agency',
        'parent_id'
    ];

    public function materials(){
        return $this->hasMany(ContractMaterial::class);
    }

    public function subs(){
        return $this->hasMany(Contract::class,'parent_id');
    }
}
