<?php
namespace app\Http\Validations\PumpOperator;

use Validator;

class FarmerPumpOperatorApplicationValidations
{
    /**
     * Scheme survey Validation 
    */
    public static function validate($request, $id = 0)
    {
        if ($request->application_type == 2) {
            $validator = Validator::make($request->all(), [
                'org_id' => 'required',
                'pump_id' => 'required',
                'name' => 'required',
                'name_bn' => 'required',
                'nid' => 'required',
                'far_mobile_no' => 'required',
                'far_division_id' => 'required',
                'far_district_id' => 'required',
                'far_upazilla_id' => 'required',
                'far_union_id' => 'required',
                'far_village' => 'required',
                'far_village_bn' => 'required',
                'date_of_birth' => 'required',
                'qualification' => 'nullable'
            ]);
        } else {
            $validator = Validator::make($request->all(), [
                'farmer_id' => 'required|unique:far_pump_opt_apps,farmer_id,'.$id,
                'org_id' => 'required',
                'pump_id' => 'required',
                'name' => 'required',
                'name_bn' => 'required',
                'nid' => 'required',
                'far_mobile_no' => 'required',
                'far_division_id' => 'required',
                'far_district_id' => 'required',
                'far_upazilla_id' => 'required',
                'far_union_id' => 'required',
                'far_village' => 'required',
                'far_village_bn' => 'required',
                'date_of_birth' => 'required',
                'qualification' => 'nullable'
            ]);
        }

        if ($validator->fails()) {
            return([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }
         return ['success'=>true];
    }
}

