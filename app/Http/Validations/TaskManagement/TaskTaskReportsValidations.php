<?php 
namespace App\Http\Validations\TaskManagement;

use Validator;
use Illuminate\Validation\Rule;

class TaskTaskReportsValidations
{
    /**
     * Task Task Reports Validations
     */
    public static function validate ($request , $id = 0)
    { 
        $task_id 			= $request->task_id;
        $validator = Validator::make($request->all(), [
            'complete_type_id' 	=> 'required',
            'attachment' 		=> 'required',
            'task_date' 		=> 'required',
            'note'              => 'required',
            'note_bn'           => 'required',
            'task_id' => [
                'required',
                Rule::unique('task_task_reports')->where(function ($query) use($task_id, $id) {
                    $query->where('task_id', $task_id);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ]

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