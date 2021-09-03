<?php
namespace app\Http\Validations\PumpOperator;

use Validator;

class PumpOperatorSuspensionValidations
{
    /**
     * Scheme survey Validation
    */
    public static function validate($request, $id = 0) {
        $validator = Validator::make($request->all(), [
            'org_id' => 'required',
            'pump_id' => 'required',
            'operator_id' => 'required',
            'reason' => 'nullable|max: 255',
            'reason_bn' => 'nullable|max: 255',
            'message' => 'nullable|max: 255',
            'message_bn' => 'nullable|max: 255',
            'suspend_date' => 'required'
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

