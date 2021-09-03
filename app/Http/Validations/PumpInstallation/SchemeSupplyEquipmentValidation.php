<?php 

namespace app\Http\Validations\PumpInstallation;

use Validator;

class SchemeSupplyEquipmentValidation
{
    /**
     * Scheme requisition Validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'requisition_id'=> 'required|unique:far_scheme_supply_equipments,requisition_id,'.$id,
            'supply_note'   => 'required',
            'supply_date'   => 'required'
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