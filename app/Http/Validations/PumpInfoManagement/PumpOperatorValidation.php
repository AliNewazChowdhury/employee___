<?php
namespace app\Http\Validations\PumpInfoManagement;

use Illuminate\Validation\Rule;
use Validator;

class  PumpOperatorValidation 
{
    /**
     * Pump Operator Validation
     */
    public static function validate($request ,$id = null)
    {
            $pump_id   = $request->pump_id;
            $validator = Validator::make($request->all(), [
                // 'pump_id' => [
                //     'required',
                //     Rule::unique('pump_operators')->where(function ($query) use( $pump_id ,$id) {
                //         $query->where('id', $pump_id);
                //         if ($id) {
                //             $query =$query->where('id', '!=' ,$pump_id);
                //         }
                //         return $query;             
                //     }),
                // ],
                // 'pump_id' => 'required|unique:pump_operators',
                'pump_id'  => 'required|unique:pump_operators,pump_id,'.$id,
                'nid'  => 'required|unique:pump_operators,nid,'.$id,
                'org_id'  => 'required',
                'name'       => 'required'
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