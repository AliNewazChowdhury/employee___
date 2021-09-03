<?php

namespace App\Models\Config;

use App\Models\PumpInstallation\PumpInstall;
use Illuminate\Database\Eloquent\Model;

class PumpProgressTypeStep extends Model
{
    protected $table ="pump_progress_type_steps";

    protected $fillable =[
        'pump_progress_type_id',	
		'step_name',	
		'step_name_bn',
		'status',
    ];

    public function pump_installation_progress_types()
    {
        return $this->belongsTo(MasterPumpInstallationProgressType::class, 'pump_progress_type_id', 'id');
    }

    public function farPumpInstall ()
    {
        return $this->hasMany(PumpInstall::class, 'pump_progress_type_step_id');
    }
}
