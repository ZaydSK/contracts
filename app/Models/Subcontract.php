<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subcontract extends Model
{
    use HasFactory;

    protected $fillable = [
        'contract_id',
        'period', 
        'number', 
        'price', 
        'subject',
        'starting_date',
        'doc_url'
    ];

    public function materialAmounts(){
        return $this->morphMany(MaterialAmount::class,'parentable');
    }
}

