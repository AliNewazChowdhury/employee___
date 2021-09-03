<?php 
namespace app\Http\Validations\PumpInstallation;

use Validator;

class ContractAgreementValidation
{
    /**
    * contract agreement validations
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'scheme_application_id' => 'required|unique:far_scheme_agreement,scheme_application_id,'.$id,
            'agreement_details'     => 'required',
            'agreement_details_bn'  => 'required'
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