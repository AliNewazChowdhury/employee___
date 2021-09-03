<?php

namespace App\Models\PumpOperator;

use Illuminate\Database\Eloquent\Model;

class FarmerPumpOperatorDocuments extends Model
{
    protected $table ="far_pump_opt_docs";

    protected $fillable = [
        'pump_opt_apps_id',	
		'dociment_title',	
		'dociment_title_bn',	
		'attachment',	
		'created_at'	
    ];
}
