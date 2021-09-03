<?php
namespace app\Http\Validations\WaterTesting;
use Validator;

class FarmerWaterTestApplicationValidations
{
    /**
     * Scheme requisition Validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'org_id'    		=> 'required',
            'name' 				=> 'required',
            'testing_type_id'   => 'required',
            'far_division_id'   => 'required',
            'far_district_id'   => 'required',
            'far_upazilla_id'  	=> 'required',
            'far_union_id'      => 'required',
            'far_village'       => 'required',
            'from_date'        	=> 'required',
            'to_date'        	=> 'required'
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