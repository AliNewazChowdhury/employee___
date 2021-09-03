<?php
namespace app\Http\Validations\PumpInstallation;

use Validator;

class SchemeReviewValidation
{
    /**
     * Scheme Project Validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'scheme_application_id' => 'required',
            'review_note'           => 'required',
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