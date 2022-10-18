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
        'number',
        'price',
        'up_price',
        'starting_date',
        'finishing_date',
        'execution_period',
        'executing_agency',
        'watching_agency'
    ];

    public function materials(){
        return $this->hasMany(ContractMaterial::class);
    }
}
