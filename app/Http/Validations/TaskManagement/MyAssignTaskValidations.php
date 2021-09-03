<?php
namespace App\Http\Validations\TaskManagement;

use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class MyAssignTaskValidations
{
    /**
     * Master Item Categories validate
     */
    public static function validate ($request , $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'meter_reading_before' 		=> 'required|max:255',
            'meter_reading_after' 		=> 'required|max:255',
            'pump_running_time' 		=> 'required|max:255',
            'irrigation_area' 		    => 'required|max:255',
            'task_date' 		        => 'required|max:255',
            'working_date' 		        => 'sometimes|nullable|max:255',
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
