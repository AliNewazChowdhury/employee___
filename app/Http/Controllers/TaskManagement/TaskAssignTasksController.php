<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Validations\TaskManagement\TaskAssignTasksValidations;
use App\Models\TaskManagement\TaskAssignTasks;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use DB;

class TaskAssignTasksController extends Controller
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
     * get all Task Assign Tasks
     */
    public function index(Request $request)
    {
        $query = DB::table('task_assign_tasks')
            ->leftJoin('task_task_reports','task_assign_tasks.id', '=','task_task_reports.task_id')
            ->select(
                'task_assign_tasks.*',
                'task_task_reports.complete_type_id'
            );

        if ($request->task_name) {
            $query = $query->where('task_name', 'like', "{$request->task_name}%")
                        ->orWhere('task_name_bn', 'like', "{$request->task_name}%");
        }

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->task_type_id) {
            $query = $query->where('task_type_id', $request->task_type_id);
        }

        if ($request->is_verified) {
		    $query = $query->where('is_verified', $request->is_verified);
		}
		if ($request->assign_user_id) {
		    $query = $query->where('assign_user_id', $request->assign_user_id);
		}

		if ($request->note) {
            $query = $query->where('note', 'like', "{$request->note}%")
                        ->orWhere('note_bn', 'like', "{$request->note}%");
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }



        $list = $query->latest()->paginate(request('per_page') ?? config('app.per_page'));

        return response([
            'success' => true,
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }

    /**
     * Task Assign Tasks store
     */
    public function store(Request $request)
    {
        $validationResult = TaskAssignTasksValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path 		= 'task-assign-tasks';
        $attachment 	=  $request->file('attachment');

        DB::beginTransaction();
        try {
			$TaskAssignTasks                    =    new TaskAssignTasks();
			$TaskAssignTasks->org_id       		=   (int)$request->org_id;
			$TaskAssignTasks->task_name       	=   $request->task_name;
			$TaskAssignTasks->task_name_bn      =   $request->task_name_bn;
			$TaskAssignTasks->task_type_id      =   (int)$request->task_type_id;
			$TaskAssignTasks->assign_user_id    =   (int)$request->assign_user_id;
			$TaskAssignTasks->assign_username   =   $request->assign_username;
			$TaskAssignTasks->note       		=   $request->note;
			$TaskAssignTasks->note_bn       	=   $request->note_bn;
			$TaskAssignTasks->task_from       	=   $request->task_from;
			$TaskAssignTasks->task_to       	=   $request->task_to;
			$TaskAssignTasks->created_by        =   (int)user_id();
			$TaskAssignTasks->updated_by        =   (int)user_id();


            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request,$file_path,'attachment');
                $TaskAssignTasks->attachment    =  $attachment_name;
                GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
            }
            $TaskAssignTasks->save();
            DB::commit();

            save_log([
                'data_id'    => $TaskAssignTasks->id,
                'table_name' => 'task_assign_tasks'
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
            'data'    => $TaskAssignTasks
        ]);
    }

    /**
     * Task Assign Tasks update
     */
    public function update(Request $request, $id)
    {
        $validationResult = TaskAssignTasksValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path 		= 'assign-tasks';
        $attachment 	=  $request->file('attachment');

        $TaskAssignTasks = TaskAssignTasks::find($id);
        $old_file 		= $TaskAssignTasks->attachment;

        if (!$TaskAssignTasks) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {

			$TaskAssignTasks->org_id       		=  (int)$request->org_id;
			$TaskAssignTasks->task_name       	=  $request->task_name;
			$TaskAssignTasks->task_name_bn      =  $request->task_name_bn;
			$TaskAssignTasks->task_type_id      =  (int)$request->task_type_id;
			$TaskAssignTasks->is_verified       =  $request->is_verified;
			$TaskAssignTasks->assign_user_id    =  (int)$request->assign_user_id;
			$TaskAssignTasks->note       		=  $request->note;
			$TaskAssignTasks->note_bn       	=  $request->note_bn;
			$TaskAssignTasks->updated_by        = (int)user_id();

			$dataSaved = $TaskAssignTasks->save();

		 	if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request,$file_path,'attachment');
				$TaskAssignTasks->attachment    =  $attachment_name;
                if($dataSaved){
                    GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name, $old_file );
                }
            }

            DB::commit();

            save_log([
                'data_id'       => $TaskAssignTasks->id,
                'table_name'    => 'task_assign_tasks',
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
            'data'    => $TaskAssignTasks
        ]);
    }

    /**
     * Task Assign Tasks status update
     */
    public function toggleStatus($id)
    {
        $TaskAssignTasks = TaskAssignTasks::find($id);

        if (!$TaskAssignTasks) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $TaskAssignTasks->status = $TaskAssignTasks->status ? 0 : 1;
        $TaskAssignTasks->update();

        save_log([
            'data_id'       => $TaskAssignTasks->id,
            'table_name'    => 'task_assign_tasks',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $TaskAssignTasks
        ]);
    }


    /* status update As Rejected*/
    public function updateIsVerified($id)
    {
        $taskAssignTasks = TaskAssignTasks::find($id);

        if (!$taskAssignTasks) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $current_status= $taskAssignTasks->is_verified;

        if ($current_status != 1){
            $taskAssignTasks->is_verified = 1;
            $taskAssignTasks->update();

        } else {
            return response([
                'success' => true,
                'message' => 'Task is already Verified',
                'data'    => $taskAssignTasks
            ]);
        }

        save_log([
            'data_id' => $taskAssignTasks->id,
            'table_name' => 'task_assign_tasks',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Task updated as Verified',
            'data'    => $taskAssignTasks
        ]);
    }

    /**
     * Task Assign Tasks destroy
     */
    public function destroy($id)
    {
        $TaskAssignTasks = TaskAssignTasks::find($id);

        if (!$TaskAssignTasks) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $TaskAssignTasks->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'task_assign_tasks',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    public function dashboard(Request $request)
    {
        $task = TaskAssignTasks::select(['id','org_id','task_name']);
        if (!empty($request->org_id)) {
            $task = $task->where('org_id', $request->org_id);
        }
        return response([
            'success' => true,
            'message' => 'Task Count',
            'data' => array(
                // 'data'=> $task->doesnthave('task_reports')->get(),
                'total_task' => $task->get()->count(),
                'complete_task' => $task->withCount(['task_reports'])->get()->sum('task_reports_count'),
                'pending_task' => $task->doesnthave('task_reports')->get()->count(),
            )
        ]);
    }

    public static function getUsers ($id, $userTypeId, $upazillaId) {
        $data = DB::table('pump_operators')
            ->join('pump_informations', 'pump_operators.pump_id', '=' , 'pump_informations.id')
            ->where('pump_informations.upazilla_id', $upazillaId)
            ->select('pump_operators.pump_operator_username as username',
                'pump_operators.pump_operator_user_id as id',
                'pump_operators.name',
                'pump_operators.name_bn'
            )
            ->get();

        if ($data) {
            return response([
                'success' => true,
                'message' => 'Task Assign User List',
                'data' => $data
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }
    }
}
