<?php
namespace app\Http\Validations\PumpInstallation;

use Validator;

class PumpStockInInfosValidations
{
    /**
     * Pump Stock In Infos Validations
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'org_id'        => 'required',
            'office_id'    	=> 'required',
            'stock_date'    => 'required',
            'item_id'       => 'required',
            'quantity'      => 'required',
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

