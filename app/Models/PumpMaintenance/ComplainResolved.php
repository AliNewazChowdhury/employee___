<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class ComplainResolved extends Model
{
    protected $table ="far_complain_resolves";

	protected $fillable = [
		'complain_id', 
		'resolve_note',	
		'created_by'	
	];
}
