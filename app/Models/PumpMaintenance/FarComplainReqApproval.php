<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class FarComplainReqApproval extends Model
{
    protected $table ="far_complain_approvals";

	protected $fillable = [
		'complain_id', 
		'requisition_id',	
		'sender_id',
		'receiver_id',
		'note'
	];
}
