<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class FarComplainRequistion extends Model
{
    protected $table ="far_complain_requisitions";

	protected $fillable = [
		'org_id', 
		'office_id',	
		'pump_type_id',
		'requisition_id',
		'requisition_date',
		'id_serial',
		'complain_id',
	];
}
