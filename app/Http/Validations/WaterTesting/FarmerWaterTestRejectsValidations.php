<?php 
namespace app\Http\Validations\WaterTesting;

use Validator;

class FarmerWaterTestRejectsValidations
{
    /**
     * Scheme note validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'note'  				  => 'required'
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