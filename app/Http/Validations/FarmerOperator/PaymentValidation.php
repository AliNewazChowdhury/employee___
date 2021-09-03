<?php
namespace app\Http\Validations\FarmerOperator;

use Validator;

class PaymentValidation
{
    /**
     * Farmer Scheme Application Validation
     */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [	
			'master_payment_id'=> 'required',
			'amount'=> 'required'
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



