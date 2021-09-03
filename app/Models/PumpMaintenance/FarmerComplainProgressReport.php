<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class FarmerComplainProgressReport extends Model
{
    protected $table ="far_complain_progress_reports";

	protected $fillable = [
		'complain_id', 
		'progress_type',	
		'note',
		'note_bn',
		'progress_date'
	];
}
