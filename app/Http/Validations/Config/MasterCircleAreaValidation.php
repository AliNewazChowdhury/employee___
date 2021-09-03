<?php
namespace app\Http\Validations\Config;

use Validator;

class MasterCircleAreaValidation
{
    /**
     * Master Circle Area Validation
     */

     public static function validate ($request)
     {
        $validator = Validator::make($request->all(), [
            'org_id'             => 'required',
            'division_id'        => 'required',
            'district_id'        => 'required',
           
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