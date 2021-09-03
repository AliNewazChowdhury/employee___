<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class PumpType extends Model
{
    protected $fillable =[
        'org_id',
		'pump_type_name',
		'pump_type_name_bn',
		'status'
    ];
}
