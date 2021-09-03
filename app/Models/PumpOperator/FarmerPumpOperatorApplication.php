<?php

namespace App\Models\PumpOperator;

use App\Models\PumpInfoManagement\PumpInfo;
use Illuminate\Database\Eloquent\Model;

class FarmerPumpOperatorApplication extends Model
{
    protected $table ="far_pump_opt_apps";

    protected $fillable = [
        'farmer_id',
		'email',
		'org_id',
		'pump_id',
		'name',
		'name_bn',
		'gender',
		'father_name',
		'father_name_bn',
		'mother_name',
		'mother_name_bn',
		'nid',
		'far_mobile_no',
		'far_division_id',
		'far_district_id',
		'far_upazilla_id',
		'far_union_id',
		'far_village',
		'far_village_bn',
		'date_of_birth',
		'qualification',
		'status',
		'payment_status',
		'final_approve',
		'created_at',
		'application_id',
		'id_serial',
		'is_renew'
    ];

    public function pump_opt_documents()
    {
        return $this->hasMany('App\Models\PumpOperator\FarmerPumpOperatorDocuments', 'pump_opt_apps_id');
    }

    public function pump_opt_rejects()
    {
        return $this->hasMany('App\Models\PumpOperator\FarmerPumpOperatorRejects', 'pump_opt_apps_id');
    }

    public function pump_opt_apps_approval()
    {
        return $this->hasMany('App\Models\PumpOperator\PumpOptAppsApproval', 'pump_opt_apps_id');
    }

    public function pump_information () {
        return $this->belongsTo(PumpInfo::class, 'pump_id');
    }

}
