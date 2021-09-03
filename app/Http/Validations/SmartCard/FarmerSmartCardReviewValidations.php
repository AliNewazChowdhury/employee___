<?php
namespace app\Http\Validations\SmartCard;
use Validator;

class FarmerSmartCardReviewValidations
{
    public static function validate($request, $id = 0)
    {
         $validator = Validator::make($request->all(), [
            'note'  => 'required'
			]);


        if ($validator->fails()) {
            return([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }
        return (['success'=>true]);
    }
}


