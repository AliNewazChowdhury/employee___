<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterPumpType extends Model
{
    protected $table ="master_pump_types";

    protected $fillable =[
        'org_id',	
		'pump_type_name',	
		'pump_type_name_bn',	
		'horse_power',	
		'horse_power_bn',
		'status',
	];
}
