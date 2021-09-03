<?php

namespace App\Models\FarmerOperator;

use Illuminate\Database\Eloquent\Model;

class FarmerComplain extends Model
{
    protected $table ="far_complains";

    protected $fillable = [
        'farmer_id',	
		'email',	
		'complain_id',	
		'id_serial',	
		'org_id',	
		'far_division_id',
		'far_district_id',	
		'far_upazilla_id',	
		'far_union_id',	
		'subject',
		'details'
    ];
}
