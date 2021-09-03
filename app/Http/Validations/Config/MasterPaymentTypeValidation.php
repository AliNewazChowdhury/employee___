<?php
namespace app\Http\Validations\Config;

use Validator;

class MasterPaymentTypeValidation
{
    /**
    * Master Payment type Validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            // 'type_name'        => 'required|unique:master_payment_types,type_name,'.$id,
            'type_name'        => 'required',
            'org_id'           => 'required',
            'amount'           => 'required',
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