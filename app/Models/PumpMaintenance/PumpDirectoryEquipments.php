<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class PumpDirectoryEquipments extends Model
{
    protected $table ="pump_directory_equipments";

	protected $fillable = [
		'pump_directory_id', 
		'master_equipment_type_id', 
		'details', 
		'details_bn',	
		'created_at'
	];
}
