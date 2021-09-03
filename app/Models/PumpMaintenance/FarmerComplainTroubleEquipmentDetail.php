<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class FarmerComplainTroubleEquipmentDetail extends Model
{
    protected $table ="far_complain_tro_equipment_details";

	protected $fillable = [
		'tro_equipments_id', 
		'name',	
		'note'
	];
}
