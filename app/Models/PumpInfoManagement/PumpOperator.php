<?php

namespace App\Models\PumpInfoManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\PumpInfoManagement\PumpInfo;


class PumpOperator extends Model
{
    protected $table ="pump_operators";

    protected $fillable = [
       'org_id','pump_id','name','name_bn','father_name','father_name_bn','mother_name','mother_name_bn','gender',
       'nid','village_name','village_name_bn','husband_name','mobile_no','email','latitude','longitude', 'daily_task_entry_required', 'pump_operator_user_id','pump_operator_username','pump_operator_email',
       'created_by', 'updated_by'
    ];

    public function pumpinfo()
    {
        return $this->hasMany(PumpInfo::class,'pump_id','pump_id');
    }

}
