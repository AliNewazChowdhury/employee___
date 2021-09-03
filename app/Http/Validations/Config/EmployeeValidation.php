<?php
namespace app\Http\Validations\Config;

use Validator;

class EmployeeValidation
{
    /**
     * Master Circle Area Validation
     */

     public static function validate ($request)
     {
        $validator = Validator::make($request->all(), [
            'name'=>'required|string',
            'address'=>'required|string',
            'designation'=>'required|string',
            'salary'=>'required|numeric',

        ]);

        if ($validator->fails()) {
           return ([
               'success' => false,
               'errors' => $validator->errors()
           ]);
         }

         return ['success'=> 'true'];

      }
     public static function validateUpdate ($request)
     {
        $validator = Validator::make($request->all(), [
            'name'=>'nullable|string',
            'address'=>'nullable|string',
            'designation'=>'nullable|string',
            'salary'=>'nullable|numeric',

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
