<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Increase extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function contract(){
        return $this->belongsTo(Contract::class);
    }

    public function material(){
        return $this->belongsTo(Contract::class);
    }

    public function materialAmounts(){
        return $this->morphMany(MaterialAmount::class,'parentable');
    }

    protected $casts = [
        'materials' => 'array',
    ];
}
