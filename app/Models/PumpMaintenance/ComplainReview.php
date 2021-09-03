<?php

namespace App\Models\PumpMaintenance;

use Illuminate\Database\Eloquent\Model;

class ComplainReview extends Model
{
    protected $table ="far_complain_reviews";

	protected $fillable = [
		'complain_id', 
		'review_note',	
		'created_by'	
	];
}
