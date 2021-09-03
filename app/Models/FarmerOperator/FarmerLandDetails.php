<?php

namespace App\Models\FarmerOperator;

use Illuminate\Database\Eloquent\Model;

class FarmerLandDetails extends Model
{
    protected $table ="farmer_land_details";

    protected $fillable = [
    	'application_id',
    	'application_type',
		'far_name',
		'far_name_bn',
		'far_father_name',
        'far_father_name_bn',
		'far_mother_name',
		'far_mother_name_bn',
		'far_division_id',
		'far_district_id',
		'far_upazilla_id',
		'far_union_id',
		'far_village',
		'far_village_bn',
		'far_nid',
		'far_mobile_no',
		'own_land_amount',
		'borga_land_amount',
		'lease_land_amount',
		'total_land_amount',
		'aus_crop_land',
		'amon_crop_land',
		'boro_crop_land',
		'other_crop_land',
		'remarks',
		'status',
		'created_at'
    ];

    public function farmer_scheme_application()
    {
        return $this->belongsTo(FarmerSchemeApplication::class, 'scheme_application_id', 'id');
    }

}
