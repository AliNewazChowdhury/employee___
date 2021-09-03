<?php
namespace app\Http\Validations\PumpInstallation;

use Validator;

class FarSchLicenseValidation
{
    /**
    * Farmer scheme license validations
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'scheme_application_id' => 'required|unique:far_scheme_license,scheme_application_id,'.$id,
            'license_no'    	    => 'required|unique:far_scheme_license,license_no,'.$id,
            'issue_date'            => 'required',
            'attachment'            => 'required'
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