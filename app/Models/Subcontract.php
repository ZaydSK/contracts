<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcontract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'agreement_date',
        'agreement_number', 
        'number', 
        'price', 
        'subject',
        'starting_date',
    ];

    public function materialAmounts(){
        return $this->morphMany(MaterialAmount::class,'parentable');
    }
}

