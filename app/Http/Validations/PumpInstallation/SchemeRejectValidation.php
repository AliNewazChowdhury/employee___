<?php
namespace app\Http\Validations\PumpInstallation;

use Validator;

class SchemeRejectValidation
{
    /**
     * Scheme reject Validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'scheme_application_id' => 'required|unique:far_scheme_rejects,scheme_application_id,'.$id,
            'reject_note'           => 'required'
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