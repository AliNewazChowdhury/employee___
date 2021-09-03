<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
class TaskTrackerController extends Controller
{

public function __construct()
    {
        //
    }

    /**
     * get all Task Tracker
     */

    public function index(Request $request)
    {
        $query = DB::table('task_assign_tasks')
                        ->leftJoin('task_review_notes','task_assign_tasks.id', '=','task_review_notes.task_id')
                        ->leftJoin('task_task_reports','task_assign_tasks.id', '=','task_task_reports.task_id')
                        ->select('task_assign_tasks.id as task_id',
                            'task_assign_tasks.task_name',
                            'task_assign_tasks.task_name_bn',
                            'task_assign_tasks.is_verified',
                            'task_assign_tasks.assign_user_id',
                            'task_assign_tasks.assign_username',
                            'task_assign_tasks.task_from',
                            'task_assign_tasks.task_to',
                            'task_assign_tasks.attachment as task_assignment_attachment',
                            'task_assign_tasks.status as task_assignment_status',
                            'task_review_notes.task_id as task_review_task_id',
                            'task_task_reports.attachment as task_reports_attachment',
                            'task_task_reports.complete_type_id',
                            'task_task_reports.task_date as task_reports_task_date',
                            'task_task_reports.note as task_reports_note',
                            'task_task_reports.note_bn as task_reports_note_bn'
                        );

        if ($request->task_id) {
            $query = $query->where('task_assign_tasks.id', $request->task_id);
        }

        if ($request->task_name) {
            $query = $query->where('task_assign_tasks.task_name', 'like', "{$request->task_name}%")
                        ->orWhere('task_assign_tasks.task_name_bn', 'like', "{$request->task_name}%");
        }
        if ($request->assign_user_id) {
            $query = $query->where('task_assign_tasks.assign_user_id', $request->assign_user_id);
        }

        if ($request->assign_username) {
            $query = $query->where('task_assign_tasks.assign_username', $request->assign_username);
        }


        $list = $query->orderBy('task_assign_tasks.created_at', 'desc')->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Task Tracker list',
            'data' => $list
        ]);
    }


/*++++++++++++++++++*/

 	public function single_index($id)
    {
        $query = DB::table('task_assign_tasks')
                        ->leftJoin('task_task_reports','task_assign_tasks.id', '=','task_task_reports.task_id')
                        ->leftJoin('task_review_notes','task_assign_tasks.id', '=','task_review_notes.task_id')
                        ->select('task_assign_tasks.id as task_id',
                            'task_assign_tasks.task_name',
                            'task_assign_tasks.task_name_bn',
                            'task_assign_tasks.is_verified',
                            'task_assign_tasks.assign_user_id',
                            'task_assign_tasks.assign_username',
                            'task_assign_tasks.attachment as task_assignment_attachment',
                            'task_assign_tasks.status as task_assignment_status',
                            'task_task_reports.attachment as task_reports_attachment',
                            'task_task_reports.task_date as task_reports_task_date',
                            'task_task_reports.note as task_reports_note',
                            'task_task_reports.note_bn as task_reports_note_bn',
                            'task_review_notes.note as task_review_note',
                            'task_review_notes.note_bn as task_review_note_bn',
                            'task_task_reports.complete_type_id'
                        );

        $query = $query->where('task_assign_tasks.id', $id);

        $list = $query->first();

        return response([
            'success' => true,
            'message' => 'Task Tracker Single data',
            'data' => $list
        ]);
    }


}
