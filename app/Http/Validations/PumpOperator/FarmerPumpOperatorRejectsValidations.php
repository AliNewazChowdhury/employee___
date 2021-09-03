<?php
namespace app\Http\Validations\PumpOperator;

use Validator;

class FarmerPumpOperatorRejectsValidations
{
    /**
     * Scheme survey Validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'reject_note' => 'required'
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