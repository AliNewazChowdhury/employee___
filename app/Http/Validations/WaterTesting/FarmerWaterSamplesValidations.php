<?php 
namespace app\Http\Validations\WaterTesting;

use Validator;

class FarmerWaterSamplesValidations
{
    /**
     * Scheme note validation 
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'laboratory_id'  		  => 'required',
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