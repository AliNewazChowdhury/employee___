<?php 
namespace app\Http\Validations\PumpInstallation;

use Validator;

class SchemeNoteValidation
{
    /**
     * Scheme note validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'scheme_application_id' => 'required|unique:far_scheme_notes,scheme_application_id,'.$id,
            'note'  => 'required'
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