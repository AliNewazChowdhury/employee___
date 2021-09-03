<?php
namespace app\Http\Validations\PumpInfoManagement;

use Illuminate\Support\Facades\Validator;

class PumpConstructionDetailsValidations
{
    /**
     * Pump information Validations
    */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'org_id'             => 'required',
            'pump_id'            => 'required',
            'division_id'        => 'required',
            'district_id'        => 'required',
            'upazilla_id'        => 'required',
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
