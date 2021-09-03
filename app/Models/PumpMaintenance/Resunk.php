<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class Resunk extends Model
{
    protected $table ="far_resunks";

	protected $fillable = [
		'complain_id', 
		'pump_informations_id', 
		'resunk_note'
	];
}
