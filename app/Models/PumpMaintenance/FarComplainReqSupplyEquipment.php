<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class FarComplainReqSupplyEquipment extends Model
{
    protected $table ="far_complain_supply_equipments";

	protected $fillable = [
		'supply_note', 
		'supply_date',	
		'requisition_id'
	];
}
