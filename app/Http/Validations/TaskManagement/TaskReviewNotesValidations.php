<?php 
namespace App\Http\Validations\TaskManagement;

use Validator;
use Illuminate\Validation\Rule;

class TaskReviewNotesValidations
{
    /**
     * Master Item Categories validate
     */
    public static function validate ($request , $id = 0)
    { 
        $task_id 	= $request->task_id;
        $validator = Validator::make($request->all(), [
            'note' => 'required',
            'note_bn' => 'required',
            'task_id' => [
                'required',
                Rule::unique('task_review_notes')->where(function ($query) use($task_id, $id) {
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