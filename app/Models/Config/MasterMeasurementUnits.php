<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterMeasurementUnits extends Model
{
     protected $table="master_measurement_units";

    protected $fillable =[
        'org_id',   
		'unit_name',    
		'unit_name_bn',    
		'created_by',
		'updated_by',
		'status'
    ];
}

