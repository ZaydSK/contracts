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
        return 'hello';
    }

  public function summed(){
    $subs = Subcontract::where('contract_id',$this->id)->pluck('id')->toArray();
    $old = ContractMaterial::where([['contract_material',1],['contract_id',$this->id]])->pluck('id')->toArray();
    $new = ContractMaterial::where([['contract_material',0],['contract_id',$this->id]])->pluck('id')->toArray();
    $old_amounts = [];
    
    $old_amounts = MaterialAmount::whereIn('material_id',$old)->groupBy('material_id')
         ->selectRaw('SUM(`quantity`) as quantity, 
         SUM(`not_used_quantity`) as not_used_quantity,
        `material_id` as material_id,
        `individual_price` as individual_price,
        `overall_price` as overall_price'
        )
        ->get();
    
    $new_amounts = MaterialAmount::whereIn('material_id',$new)->get();
    return [
        'contract' => $old_amounts,
        'other' => $new_amounts
    ];
  }
}
