<?php

namespace App\Models\PumpInfoManagement;

use Illuminate\Database\Eloquent\Model;
use App\Models\PumpInfoManagement\PumpOperator;

class PumpInfo extends Model
{
    protected $table = "pump_informations";

    protected $fillable = [
        'org_id','pump_id','project_id','division_id','district_id','upazilla_id','union_id',
        'mouza_no','jl_no','plot_no','water_group_id','latitude','longitude'
    ];

    
}
