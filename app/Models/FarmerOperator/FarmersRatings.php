<?php

namespace App\Models\FarmerOperator;

use Illuminate\Database\Eloquent\Model;

class FarmersRatings extends Model
{
   protected $table ="far_ratings";

    protected $fillable = [
    	'feedback',	
		'feedback_bn',	
		'rating',	
		'org_id',	
		'division_id',	
		'district_id',	
		'upazilla_id',	
		'farmer_id',	
		'created_at',	
		'updated_at'
    ];
}
