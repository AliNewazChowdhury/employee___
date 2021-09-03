<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class FarmerComplainTroubleEquipment extends Model
{
    protected $table ="far_complain_tro_equipments";

	protected $fillable = [
		'complain_id', 
		'division_id',	
		'district_id',
		'upazilla_id',
		'union_id',
		'pump_id',
		'jl_no',
		'plot_no',
	];
}
