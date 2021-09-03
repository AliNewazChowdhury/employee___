<?php

namespace App\Models\FarmerProfile;

use Illuminate\Database\Eloquent\Model;

class FarmerBasicInfos extends Model
{
    protected $table ="far_basic_infos";
    
	protected $fillable = [
		'farmer_id',	
		'email',	
		'name',	
		'name_bn',	
		'father_name',	
		'father_name_bn',	
		'mother_name',	
		'mother_name_bn',	
		'nid',	
		'far_division_id',	
		'far_district_id',	
		'far_upazilla_id',	
		'far_union_id',	
		'far_village',	
		'far_village_bn',	
		'status', 	
		'created_at',	
		'updated_at',
		'mobile_no'
	];
}
