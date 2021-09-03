<?php
namespace app\Http\Validations\SmartCard;
use Validator;

class FarmerSmartCardApplicationValidations
{
    public static function validate($request, $id = 0)
    {
         $validator = Validator::make($request->all(), [
            'attachment'        => 'required|mimes:jpg,png,jpeg',
            'org_id'        	=> 'required',
			'email'        		=> 'required',
			'name'        		=> 'required',
			'father_name'       => 'required',
			'mother_name'       => 'required',
			'marital_status'    => 'required',
			'nid'        		=> 'required',
			'mobile_no'        	=> 'required',
			'gender'        	=> 'required',
			'far_division_id'   => 'required',
			'far_district_id'   => 'required',
			'far_upazilla_id'   => 'required',
			'far_union_id'      => 'required',
			'far_village'       => 'required',
			'ward_no'        	=> 'required',
			'date_of_birth'     => 'required',
			]);


        if ($validator->fails()) {
            return([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }
        return (['success'=>true]);
    }
}


