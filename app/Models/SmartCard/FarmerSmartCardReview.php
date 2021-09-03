<?php

namespace App\Models\SmartCard;

use Illuminate\Database\Eloquent\Model;

class FarmerSmartCardReview extends Model
{
    protected $table ="far_smart_card_review";
    
	protected $fillable = [
		'far_smart_card_apps_id',	
		'note',	
		'note_bn',	
		'created_at'
	];
}
