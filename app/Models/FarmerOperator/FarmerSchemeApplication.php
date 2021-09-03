<?php

namespace App\Models\FarmerOperator;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
class FarmerSchemeApplication extends Model
{
    protected $table ="far_scheme_application";

    protected $fillable = [
        'farmer_id',
		'email',
		'application_id',
		'scheme_type_id',
		'sub_scheme_type_id',
		'org_id',
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
		'far_mobile_no',
		'total_farmer',
		'added_farmer',
		'status',
		'payment_status'
    ];
	public static function farmerApplicationNumber()
    {
        $applicationIdGet = DB::table('far_scheme_application')
                                    ->select('application_id')
                                    ->orderBy('id','desc')
                                    ->first();

		if($applicationIdGet){
			$application_id = $applicationIdGet->application_id;
			if( $application_id !="" ){
				$application_id+= 1;
			}
		} else {
			$application_id = 100000;
		}
		return $application_id;
    }
    public function farmer_land_details()
    {
        return $this->hasMany(FarmerLandDetails::class, 'scheme_application_id', 'id');
    }

}






