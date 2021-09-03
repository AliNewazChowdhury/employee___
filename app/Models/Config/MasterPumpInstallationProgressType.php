<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterPumpInstallationProgressType extends Model
{

    protected $table ="master_pump_progress_types";

    protected $fillable =[
        'org_id',	
		'pump_type_id',	
		'application_type_id',
		'status',
    ];

    public function pump_type()
    {
        return $this->belongsTo(MasterPumpType::class, 'pump_type_id', 'id');
    }

    public function steps()
    {
        return $this->hasMany(PumpProgressTypeStep::class, 'pump_progress_type_id');
    }
}
