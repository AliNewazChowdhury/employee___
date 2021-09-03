<?php

namespace App\Models\PumpOperator;

use Illuminate\Database\Eloquent\Model;

class FarmerPumpOperatorRejects extends Model
{
    protected $table ="far_pump_opt_rejects";

    protected $fillable = [
    	'pump_opt_apps_id',	
		'reject_note',	
		'reject_note_bn',	
		'attachment',
		'created_at'       	
    ];


    public function pump_opt_application()
    {
        return $this->belongsTo(FarmerPumpOperatorApplication::class);
    }
}
