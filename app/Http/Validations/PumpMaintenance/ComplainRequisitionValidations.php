<?php
namespace app\Http\Validations\PumpMaintenance;

use Validator;

class ComplainRequisitionValidations
{
    /**
     * Farmer Basic Infos Validation
     */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'complain_id' => 'required|unique:far_complain_requisitions,complain_id,'.$id,
            'org_id'    => 'required',
            'office_id' => 'required',
            'pump_type_id'      => 'required',
            'requisition_id'    => 'required',
            'requisition_date'  => 'required',
            'item_id.*'         => 'required',
            'quantity.*'        => 'required'
        ]);

       if ($validator->fails()) {
           return ([
               'success' => false,
               'errors' => $validator->errors()
           ]);
       }

       return ['success'=> 'true'];

    }
}
