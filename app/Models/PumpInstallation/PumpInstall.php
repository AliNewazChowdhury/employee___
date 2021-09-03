<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class PumpInstall extends Model
{
    protected $table="far_pump_install";

    protected $fillable = [ 
        'scheme_application_id', 
        'contractor_id', 
        'pump_progress_types_id', 
        'start_date', 
        'end_date'
    ];
}
