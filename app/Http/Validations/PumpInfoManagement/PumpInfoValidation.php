<?php
namespace app\Http\Validations\PumpInfoManagement;

use Illuminate\Support\Facades\Validator;

class PumpInfoValidation
{
    /**
     * Pump information Validations
    */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'org_id'             => 'required',
            'pump_id'            => 'required|unique:pump_informations,pump_id,'.$request->id,
            'total_farmer'       => 'required|numeric',
            'division_id'        => 'required',
            'district_id'        => 'required',
            'upazilla_id'        => 'required',
            'union_id'           => 'required'
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
