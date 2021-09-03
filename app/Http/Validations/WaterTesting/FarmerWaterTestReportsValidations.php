<?php 
namespace app\Http\Validations\WaterTesting;

use Validator;

class FarmerWaterTestReportsValidations
{
    /**
     * Scheme note validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'memo_no'  		 => 'required'
            /*'attachment'  	 => 'required'*/
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
