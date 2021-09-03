<?php

namespace App\Models\SmartCard;

use Illuminate\Database\Eloquent\Model;

class FarmerSmartCardRejects extends Model
{
    protected $table ="far_smart_card_rejects";

	protected $fillable = [
		'far_smart_card_apps_id',	
		'reject_note',	
		'reject_note_bn',	
		'created_at'
	];
}
