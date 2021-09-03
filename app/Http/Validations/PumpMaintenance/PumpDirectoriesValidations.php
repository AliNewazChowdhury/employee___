<?php
namespace app\Http\Validations\PumpMaintenance;

use Illuminate\Validation\Rule;
use Validator;

class  PumpDirectoriesValidations
{
    /**
     * Farmer Basic Infos Validation
     */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'type_id'    			=> 'required',
            'name'    				=> 'required',
            'name_bn'    			=> 'required',
            'village_name'   		=> 'required',
            'village_name_bn'       => 'required',
            'address'    			=> 'required',
            'address_bn'    		=> 'required',
            'mobile'    			=> 'required',
            'division_id'    		=> 'required',
            'district_id'    		=> 'required',
            'upazila_id'    		=> 'required',
            'union_id'    			=> 'required',
            // 'master_equipment_type_id'  => 'required',
            // 'details'    				=> 'required',
            // 'details_bn'    		=> 'required'
        ]);


       if ($validator->fails()) {
           return ([
               'success' => false,
               'errors' => $validator->errors()
           ]);
       }

       return ['success'=> 'true'];

    }
}
