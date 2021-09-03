<?php
namespace app\Http\Validations\PumpInfoManagement;

use Validator;

class PumpSchedulerValidation
{
    /**
     * Pump Scheduler Validation
    */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'org_id'             => 'required',
            'pump_id'            => 'required|unique:pump_schedulers,pump_id,'.$request->id,
            'ontime'             => 'required',
            'offtime'            => 'required',
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