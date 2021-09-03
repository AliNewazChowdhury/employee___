<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class TaskCalendarController extends Controller
{

	public function __construct()
    {
        //
    }

    /**
     * get all Task Assign Tasks
     */

    public function index(Request $request)
    {
        $query = DB::table('task_assign_tasks')
                        ->leftJoin('task_task_reports','task_assign_tasks.id', '=','task_task_reports.task_id')
                        ->leftJoin('task_review_notes','task_assign_tasks.id', '=','task_review_notes.task_id')
                        ->where('task_assign_tasks.assign_user_id', user_id())
                        ->select('task_assign_tasks.id as task_id',
                            'task_assign_tasks.task_name',
                            'task_assign_tasks.task_name_bn',
                            'task_assign_tasks.is_verified',
                            'task_assign_tasks.assign_username',
                            'task_assign_tasks.attachment as task_assignment_attachment',
                            'task_assign_tasks.note as task_assignment_note',
                            'task_assign_tasks.note_bn as task_assignment_note_bn',
                            'task_assign_tasks.status as task_assignment_status',
                            'task_task_reports.task_date as task_calendar_date',
                            'task_task_reports.complete_type_id',
                            'task_task_reports.note as task_task_reports_note',
                            'task_task_reports.note_bn as task_task_reports_note_bn',
                            'task_task_reports.attachment as task_task_reports_attachment',
                            'task_task_reports.task_date as task_task_reports_task_date',
                            'task_review_notes.note as task_review_note',
                            'task_review_notes.note_bn as task_review_note_bn'
                        );
        if ($request->task_id) {
            $query = $query->where('task_assign_tasks.id', $request->task_id);
        }

        if ($request->task_name) {
            $query = $query->where('task_assign_tasks.task_name', 'like', "{$request->task_name}%")
                        ->orWhere('task_assign_tasks.task_name_bn', 'like', "{$request->task_name}%");
        }
        if ($request->is_verified) {
            $query = $query->where('task_assign_tasks.is_verified', $request->is_verified);
        }

        if ($request->task_note) {
            $query = $query->where('task_assign_tasks.note', 'like', "%{$request->task_note}%")
                        ->orWhere('task_assign_tasks.note_bn', 'like', "%{$request->task_note}%");
        }


        if ($request->review_note) {
            $query = $query->where('task_review_notes.note', 'like', "%{$request->review_note}%")
                        ->orWhere('task_review_notes.note_bn', 'like', "%{$request->review_note}%");
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Task assign_tasks list',
            'data' => $list
        ]);
    }

    public function backup_index(Request $request)
    {
        $query = DB::table('task_assign_tasks')
        				->join('task_task_reports','task_assign_tasks.id', '=','task_task_reports.task_id')
                        ->select('task_assign_tasks.id as task_id',
                            'task_assign_tasks.task_name',
                            'task_assign_tasks.task_name_bn',
                            'task_assign_tasks.is_verified',
                            'task_assign_tasks.assign_username',
                            'task_assign_tasks.attachment as task_assignment_attachment',
                            'task_assign_tasks.note as task_assignment_note',
                            'task_assign_tasks.note_bn as task_assignment_note_bn',
                            'task_assign_tasks.status as task_assignment_status',
                            'task_task_reports.task_date as task_calendar_date',
                            'task_review_notes.note as task_review_note',
                            'task_review_notes.note_bn as task_review_note_bn'
                    	);


        if ($request->task_name) {
            $query = $query->where('task_assign_tasks.task_name', 'like', "{$request->task_name}%")
                        ->orWhere('task_assign_tasks.task_name_bn', 'like', "{$request->task_name}%");
        }

        if ($request->assign_user_id) {
            $query = $query->where('task_assign_tasks.assign_user_id', $request->assign_user_id);
        }

        if ($request->task_type_id) {
            $query = $query->where('task_assign_tasks.task_type_id', $request->task_type_id);
        }

        if ($request->is_verified) {
		    $query = $query->where('task_assign_tasks.is_verified', $request->is_verified);
		}

		if ($request->task_report_note) {
            $query = $query->where('task_task_reports.note', 'like', "{$request->task_report_note}%")
                        ->orWhere('task_task_reports.note_bn', 'like', "{$request->task_report_note}%");
        }

        if ($request->status) {
            $query = $query->where('task_assign_tasks.status', $request->status);
        }

        $list = $query->paginate(request('per_page') ?? config('app.per_page'));

        return response([
            'success' => true,
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }


    public function single_index(Request $request, $id )
    {
        $query = DB::table('task_assign_tasks')
                ->leftJoin('task_task_reports','task_assign_tasks.id', '=','task_task_reports.task_id')
                ->leftJoin('task_review_notes','task_assign_tasks.id', '=','task_review_notes.task_id')
                ->select('task_assign_tasks.id as task_id',
                    'task_assign_tasks.task_name',
                    'task_assign_tasks.task_name_bn',
                    'task_assign_tasks.is_verified',
                    'task_assign_tasks.assign_user_id',
                    'task_assign_tasks.task_from',
                    'task_assign_tasks.task_to',
                    'task_assign_tasks.assign_username',
                    'task_assign_tasks.attachment as task_assignment_attachment',
                    'task_assign_tasks.note as task_assignment_note',
                    'task_assign_tasks.note_bn as task_assignment_note_bn',
                    'task_assign_tasks.status as task_assignment_status',
                    'task_task_reports.task_date as task_calendar_date',
                    'task_task_reports.complete_type_id',
                    'task_task_reports.note as task_task_reports_note',
                    'task_task_reports.note_bn as task_task_reports_note_bn',
                    'task_task_reports.attachment as task_task_reports_attachment',
                    'task_task_reports.task_date as task_task_reports_task_date',

                    'task_review_notes.note as task_review_note',
                    'task_review_notes.note_bn as task_review_note_bn'
                );

        if ($request->task_name) {
            $query = $query->where('task_assign_tasks.task_name', 'like', "{$request->task_name}%")
                        ->orWhere('task_assign_tasks.task_name_bn', 'like', "{$request->task_name}%");
        }

        if ($request->task_type_id) {
            $query = $query->where('task_assign_tasks.task_type_id', $request->task_type_id);
        }

        if ($request->is_verified) {
		    $query = $query->where('task_assign_tasks.is_verified', $request->is_verified);
		}

		if ($request->note) {
            $query = $query->where('task_task_reports.note', 'like', "{$request->note}%")
                        ->orWhere('task_task_reports.note_bn', 'like', "{$request->note}%");
        }

        if ($request->status) {
            $query = $query->where('task_assign_tasks.status', $request->status);
        }
        $query = $query->where('task_assign_tasks.id', $id);
        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }
}
