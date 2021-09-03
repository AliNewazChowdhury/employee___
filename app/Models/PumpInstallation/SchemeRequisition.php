<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeRequisition extends Model
{
    protected $table ="far_scheme_requisitions";

    protected $fillable =[
        'org_id','office_id','pump_type_id','horse_power_id','requisition_id','requisition_date','id_serial','scheme_application_id'
    ];
}
