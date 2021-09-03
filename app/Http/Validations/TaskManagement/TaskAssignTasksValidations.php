<?php
namespace App\Http\Validations\TaskManagement;

use Validator;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class TaskAssignTasksValidations
{
    /**
     * Master Item Categories validate
     */
    public static function validate ($request , $id = 0)
    {
        $org_id 			= $request->org_id;
        $assign_user_id 	= $request->assign_user_id;
        $task_name      	= $request->task_name;
        $task_name_bn   	= $request->task_name_bn;

        $validator = Validator::make($request->all(), [
            'org_id' 			=> 'required',
            'task_type_id' 		=> 'required',
            'assign_user_id' 	=> 'required',
            'assign_username' 	=> 'required',
            'note' 				=> 'required',
            'note_bn' 			=> 'required',
            'task_name' => [
                'required',
                Rule::unique('task_assign_tasks')->where(function ($query) use($org_id, $assign_user_id, $task_name, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('assign_user_id', $assign_user_id,);
                    $query->where('task_name', $task_name);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;
                }),
            ],

            'task_name_bn' => [
                'required',
                Rule::unique('task_assign_tasks')->where(function ($query) use($org_id, $assign_user_id, $task_name_bn, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('assign_user_id', $assign_user_id,);
                    $query->where('task_name_bn', $task_name_bn);
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
