<?php

namespace App\Models\WaterTesting;

use Illuminate\Database\Eloquent\Model;

class WaterTestingParameter extends Model
{
    protected $table="water_testing_parameters";

    protected $fillable =[
        'name',
		'name_bn',
		'org_id',	
		'testing_type_id',
		'status',
		'created_at',
		'updated_at'
    ];
}
