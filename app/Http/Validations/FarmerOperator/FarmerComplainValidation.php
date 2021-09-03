<?php
namespace app\Http\Validations\FarmerOperator;

use Validator;

class FarmerComplainValidation
{
    /**
     * Farmer Complain Validation
     */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [	
			'farmer_id'         => 'required',
			'email'             => 'required',
			'org_id'            => 'required',
			'far_division_id'   => 'required',
			'far_district_id'   => 'required',
			'far_upazilla_id'   => 'required',
			'far_union_id'      => 'required',
			'subject'           => 'required',
			'details'           => 'required'
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



