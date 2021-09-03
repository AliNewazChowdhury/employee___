<?php
namespace app\Http\Validations\FarmerOperator;

use Validator;

class FarmerLandDetailsValidations
{
    /**
     * Farmer Complain Validation
     */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [
				'application_id'            => 'required',
				'application_type'          => 'required',
				'far_name'     				=> 'required',
				'far_father_name'     		=> 'required',
				'far_mother_name'     		=> 'required',
				'far_division_id'     		=> 'required',
				'far_district_id'     		=> 'required',
				'far_upazilla_id'     		=> 'required',
				'far_union_id'     			=> 'required',
				'far_village'     			=> 'required',
				'far_nid'     				=> 'required',
				'far_mobile_no'     		=> 'required'
        ]);

        if ($validator->fails()) {
            return([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }

        return ['success'=>true];
    }
}



