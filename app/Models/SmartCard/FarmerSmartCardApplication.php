<?php

namespace App\Models\SmartCard;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FarmerSmartCardApplication extends Model
{
    protected $table ="far_smart_card_apps";

    use SoftDeletes;    
    protected $fillable = [
        'org_id',	
		'farmer_id',	
		'id_serial',	
		'application_id',	
		'email',	
		'name',	
		'name_bn',	
		'father_name',	
		'father_name_bn',	
		'mother_name',	
		'mother_name_bn',	
		'marital_status',	
		'spouse_name',	
		'spouse_name_bn',	
		'no_of_child',	
		'nid',	
		'mobile_no',	
		'gender',	
		'far_division_id',	
		'far_district_id',	
		'far_upazilla_id',	
		'far_union_id',	
		'far_village',	
		'far_village_bn',	
		'ward_no',	
		'date_of_birth',	
		'qualification',	
		'attachment',	
		'owned_land',	
		'lease_land',	
		'barga_land',	
		'total_land',	
		'training_info',	
		'earnings',	
		'crop_plan',	
		'crop_plan_bn',
		'status',
		'reissue_status'
    ];

    public function smartCardReview(){
    	return $this->hasMany(FarmerSmartCardReview::class, 'far_smart_card_apps_id');
    }
    public function smartCardRejects(){
    	return $this->hasOne(FarmerSmartCardRejects::class, 'far_smart_card_apps_id');
    }
}
