<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class FarComplainReqDetails extends Model
{
    protected $table ="far_complain_req_details";

	protected $fillable = [
		'requisition_id', 
		'item_id',	
		'quantity',
		'accepted_quantity'
	];
}
