<?php

namespace App\Models\PumpOperator;

use Illuminate\Database\Eloquent\Model;

class FarmerPumpOperatorReniewsReject extends Model
{
   	protected $table ="far_pump_opt_reniews_reject";

	protected $fillable = [
		'pump_opt_apps_id',	
		'renew_id',	
		'reject_note',	
		'reject_note_bn',	
		'attachment',
		'created_at'       	
	];

	public function pump_operator_reniews(){
		 return $this->belongsTo(FarmerPumpOperatorApplicationReniews::class, 'renew_id','id');
	}
}
