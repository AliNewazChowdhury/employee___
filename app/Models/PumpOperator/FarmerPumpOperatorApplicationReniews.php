<?php

namespace App\Models\PumpOperator;

use Illuminate\Database\Eloquent\Model;

class FarmerPumpOperatorApplicationReniews extends Model
{
    protected $table ="far_pump_opt_app_reniews";

	protected $fillable = [
		'pump_opt_apps_id',	
		'application_date',	
		'payment_status',	
		'status', 
		'created_at'      	
	];


	public function pump_opt_application()
    {
        return $this->belongsTo(FarmerPumpOperatorApplication::class);
    }
}
