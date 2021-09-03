<?php
namespace app\Http\Validations\PumpInstallation;

use Validator;

class SchemeRequisitionValidation
{
    /**
     * Scheme requisition Validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'scheme_application_id' => 'required|unique:far_scheme_requisitions,scheme_application_id,'.$id,
            'org_id'    => 'required',
            'office_id' => 'required',
            'pump_type_id'      => 'required',
            'requisition_id'    => 'required',
            'requisition_date'  => 'required',
            'item_id.*'         => 'required',
            'quantity.*'        => 'required'
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