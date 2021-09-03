<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Validations\TaskManagement\TaskReviewNotesValidations;
use App\Models\TaskManagement\TaskReviewNotes;
use Illuminate\Http\Request;
use DB;

class TaskReviewNotesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * get all Task Review Notes
     */
    public function index(Request $request)
    {
        $query = DB::table('task_review_notes')
        			->join('task_assign_tasks','task_review_notes.task_id', '=','task_assign_tasks.id')
                    ->select('task_review_notes.*',
                        'task_assign_tasks.id as assign_task_id',
                        'task_assign_tasks.task_name',
                        'task_assign_tasks.task_name_bn'
             			);


		if ($request->note) {
            $query = $query->where('note', 'like', "{$request->note}%")
                        ->orWhere('note_bn', 'like', "{$request->note}%");
        }

        if ($request->task_id) {
		    $query = $query->where('task_assign_tasks.id', $request->task_id);
		}

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->paginate(request('per_page') ?? config('app.per_page'));

        return response([
            'success' => true,
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }

    /**
     * Task Review Notes store
     */
    public function store(Request $request)
    {
        $validationResult = TaskReviewNotesValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }


        DB::beginTransaction();
        try {
			$TaskReviewNotes                    = new TaskReviewNotes();
			$TaskReviewNotes->task_id    		=  (int)$request->task_id;
			$TaskReviewNotes->note       		=  $request->note;
			$TaskReviewNotes->note_bn       	=  $request->note_bn;
			$TaskReviewNotes->created_by        = (int)user_id();
			$TaskReviewNotes->updated_by        = (int)user_id();
			$TaskReviewNotes->save();


			DB::commit();

            save_log([
                'data_id'    => $TaskReviewNotes->id,
                'table_name' => 'task_review_notes'
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $TaskReviewNotes
        ]);
    }

    /**
     * Task Review Notes update
     */
    public function update(Request $request, $id)
    {
        $validationResult = TaskReviewNotesValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path 		= 'assign-tasks';
        $attachment 	=  $request->file('attachment');

        $TaskReviewNotes = TaskReviewNotes::find($id);
        $old_file 		= $TaskReviewNotes->attachment;

        if (!$TaskReviewNotes) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {

			$TaskReviewNotes->task_id    		=  (int)$request->task_id;
			$TaskReviewNotes->note       		=  $request->note;
			$TaskReviewNotes->note_bn       	=  $request->note_bn;
			$TaskReviewNotes->updated_by        = (int)user_id();

			$TaskReviewNotes->save();

            DB::commit();

            save_log([
                'data_id'       => $TaskReviewNotes->id,
                'table_name'    => 'task_review_notes',
                'execution_type'=> 1
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $TaskReviewNotes
        ]);
    }

    /**
     * Task Review Notes status update
     */
    public function toggleStatus($id)
    {
        $TaskReviewNotes = TaskReviewNotes::find($id);

        if (!$TaskReviewNotes) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $TaskReviewNotes->status = $TaskReviewNotes->status ? 0 : 1;
        $TaskReviewNotes->update();

        save_log([
            'data_id'       => $TaskReviewNotes->id,
            'table_name'    => 'task_review_notes',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $TaskReviewNotes
        ]);
    }

    /**
     * Task Review Notes destroy
     */
    public function destroy($id)
    {
        $TaskReviewNotes = TaskReviewNotes::find($id);

        if (!$TaskReviewNotes) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $TaskReviewNotes->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'task_review_notes',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
