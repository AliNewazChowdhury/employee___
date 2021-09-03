<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Validations\TaskManagement\TaskTaskReportsValidations;
use App\Models\TaskManagement\TaskTaskReports;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use Illuminate\Support\Facades\DB;

class TaskTaskReportsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //dd('ok');
    }

    /**
     * get all Task Task Reports
     */
    public function index(Request $request)
    {
        $query = DB::table('task_task_reports')
        			->join('task_assign_tasks','task_task_reports.task_id', '=','task_assign_tasks.id')
                    ->select('task_task_reports.*',
                        'task_assign_tasks.id as assign_task_id',
                        'task_assign_tasks.task_name',
                        'task_assign_tasks.task_name_bn'
             			);

        if ($request->note) {
            $query = $query->where('note', 'like', "{$request->note}%")
                        ->orWhere('note_bn', 'like', "{$request->note}%");
        }

        // if ($request->task_id) {
        //     $que ry = $query->where('task_assign_tasks.id', $request->task_id);
        // }

        if ($request->complete_type_id) {
            $query = $query->where('complete_type_id', $request->complete_type_id);
        }

		if ($request->task_date) {
		    $query = $query->where('task_date', $request->task_date);
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


    /**************/


    public function all_reports(Request $request)
    {
        $query = $this->taskAssignReportQuery();
        // Show Daily Task Report
        if ($request->type == 1) {
            $query = DB::table('my_assign_tasks')
                    ->leftJoin('far_basic_infos', 'my_assign_tasks.farmer_user_id', '=', 'far_basic_infos.farmer_id')
                    ->select('my_assign_tasks.*', 'far_basic_infos.*');

            if ($request->org_id) {
                $query = $query->where('my_assign_tasks.org_id', $request->org_id);
            }

            if ($request->task_id) {
                $query = $query->where('my_assign_tasks.id', $request->task_id);
            }
        }
        if (!$request->type || $request->type == 2) {
            $query = $this->taskAssignReportQuery();

            if ($request->task_id) {
                $query = $query->where('task_assign_tasks.id', $request->task_id);
            }

            if ($request->task_name) {
                $query = $query->where('task_assign_tasks.task_name', 'like', "{$request->task_name}%")
                    ->orWhere('task_assign_tasks.task_name_bn', 'like', "{$request->task_name}%");
            }

            if ($request->task_assign_status) {
                $query = $query->where('task_assign_tasks.status', $request->task_assign_status);
            }

            if ($request->org_id) {
                $query = $query->where('task_assign_tasks.org_id', $request->org_id);
            }

            if ($request->from_date && $request->to_date)
            {
                $startDate   = date('Y-m-d', strtotime($request->from_date));
                $endDate     = date('Y-m-d', strtotime($request->to_date));
                $query       = $query->whereBetween('task_assign_tasks.created_at', [$startDate, $endDate]);
            }

            if ($request->from_date && !isset($request->to_date))
            {
                $query = $query->whereDate('task_assign_tasks.created_at', '<=', date('Y-m-d', strtotime($request->from_date)));
            }

            if (!$request->from_date && isset($request->to_date))
            {
                $query = $query->whereDate('task_assign_tasks.created_at', '>=', date('Y-m-d', strtotime($request->to_date)));
            }

        }

        $list = $query->paginate(request('per_page') ?? config('app.per_page'));
        return response([
            'success' => true,
            'message' => 'My Assign Tasks list',
            'data' => $list,
            'type' => $request->type
        ]);
    }
    /**
     * Task Task Reports store
     */
    public function store(Request $request)
    {

        $validationResult = TaskTaskReportsValidations::validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path 		= 'task-reports';
        $attachment 	=  $request->file('attachment');


        DB::beginTransaction();
       // try {
			$TaskTaskReports                    = new TaskTaskReports();
			$TaskTaskReports->task_id      		=  (int)$request->task_id;
			$TaskTaskReports->complete_type_id  =  (int)$request->complete_type_id;
			$TaskTaskReports->note       		=  $request->note;
			$TaskTaskReports->note_bn       	=  $request->note_bn;
            $TaskTaskReports->task_date         =  $request->task_date;
			$TaskTaskReports->created_by        = (int)user_id();
			$TaskTaskReports->updated_by        = (int)user_id();

		 	if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
            }

			$TaskTaskReports->attachment       	=  $attachment_name ? $attachment_name : null;

			if($TaskTaskReports->save()){

				 GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
			}


			DB::commit();

            save_log([
                'data_id'    => $TaskTaskReports->id,
                'table_name' => 'task_task_reports'
            ]);

       /* } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }*/

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $TaskTaskReports
        ]);
    }

    /**
     * Task Task Reports update
     */
    public function update(Request $request, $id)
    {
        $validationResult = TaskTaskReportsValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path 		= 'task-reports';
        $attachment 	=  $request->file('attachment');

        $TaskTaskReports = TaskTaskReports::find($id);
        $old_file 		= $TaskTaskReports->attachment;

        if (!$TaskTaskReports) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {

			$TaskTaskReports->task_id      		=  (int)$request->task_id;
			$TaskTaskReports->complete_type_id  =  (int)$request->complete_type_id;
			$TaskTaskReports->note       		=  $request->note;
			$TaskTaskReports->note_bn       	=  $request->note_bn;
            $TaskTaskReports->task_date         =  $request->task_date;
			$TaskTaskReports->updated_by        = (int)user_id();

		 	if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request,$file_path,'attachment');
				$TaskTaskReports->attachment    =  $attachment_name;
            }

			if($TaskTaskReports->save()){
				 GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name, $old_file );
			}

            DB::commit();
            save_log([
                'data_id'       => $TaskTaskReports->id,
                'table_name'    => 'task_task_reports',
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
            'data'    => $TaskTaskReports
        ]);
    }

    /**
     * Task Task Reports status update
     */
    public function toggleStatus($id)
    {
        $TaskTaskReports = TaskTaskReports::find($id);

        if (!$TaskTaskReports) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $TaskTaskReports->status = $TaskTaskReports->status ? 0 : 1;
        $TaskTaskReports->update();

        save_log([
            'data_id'       => $TaskTaskReports->id,
            'table_name'    => 'task_task_reports',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $TaskTaskReports
        ]);
    }

    /**
     * Task Task Reports destroy
     */
    public function destroy($id)
    {
        $TaskTaskReports = TaskTaskReports::find($id);

        if (!$TaskTaskReports) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $TaskTaskReports->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'task_task_reports',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    public function taskAssignReportQuery() {
        return DB::table('task_task_reports')
            ->rightJoin('task_assign_tasks','task_task_reports.task_id', '=','task_assign_tasks.id')
            ->select('task_task_reports.attachment as task_reports_attachment',
                'task_task_reports.task_date',
                'task_task_reports.note as task_reports_note',
                'task_task_reports.note_bn as task_reports_note_bn',
                'task_assign_tasks.id as task_id',
                'task_assign_tasks.org_id',
                'task_assign_tasks.task_name',
                'task_assign_tasks.task_name_bn',
                'task_assign_tasks.assign_user_id',
                'task_assign_tasks.assign_username',
                'task_assign_tasks.attachment as task_assign_attachment',
                'task_assign_tasks.status as task_assign_status'
            );
    }
}
