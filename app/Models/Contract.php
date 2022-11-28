<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;
    protected $fillable = [
        'project_name', // اسم المشروع
        'date', // التاريخ
        'branch', // الفرع
        'area_name', // المضمون
        'up_percent', // نسبة الضم
        'down_percent', // نسبة التنزيل 
        'stoppings_percent', // نسبة التوقيفات
        'number', // رقم العقد
        'price', // السعر
        'up_price', // بدل الاحالة
        'starting_date', // تاريخ البداية
        //'finishing_date', // تاريخ الانتهاء
        'virtual_finishing_date',  // تاريخ الانتهاء المتوقع
        'execution_period', // مدة التنفيذ
        'executing_agency', // الجهة المنفذة
        'watching_agency', // الجهة المشرفة
    ];

    public function materials(){
        return $this->hasMany(ContractMaterial::class);
    }

    public function subs(){
        return $this->hasMany(Subcontract::class);
    }

    public function bills(){
        return $this->hasMany(Bill::class);
    }

    public function materialAmounts(){
        return $this->morphMany(MaterialAmount::class,'parentable');
    }
}
