<?php
namespace app\Http\Validations\PumpInstallation;

use Validator;

class SchemeSurveyValidation
{
    /**
     * Scheme survey Validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'scheme_application_id' => 'required',
            'scheme_application_id' => 'required',
            'survey_date'           => 'required',
            'suggestion'            => 'required',
            'note.*'                => 'required'
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