<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterPumpCapacity extends Model
{
    protected $table = "master_pump_capacities";

    protected $fillable =[
        'capacity',	
        'org_id',	
		'master_scheme_type_id',
		'status',
		'created_by',
		'updated_by'
    ];
}
