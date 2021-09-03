<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use App\Http\Validations\TaskManagement\MyAssignTaskValidations;
use App\Models\TaskManagement\MyAssignTask;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MyDailyTaskController extends Controller
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
        $this->checkDailyTask();

        $query = MyAssignTask::where('farmer_user_id', (int)user_id());

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->from_date) {
            $query->where('task_date', '>=', $request->from_date);
        }

        if ($request->to_date) {
            $query->where('task_date', '<=', $request->to_date);
        }

        $list = $query->paginate($request->per_page ?? config('app.per_page'));

        return response([
            'success' => true,
            'message' => 'My Assign Task List',
            'data' => $list
        ]);
    }

    /**
     * My Assign Task store
     */
    public function store(Request $request)
    {
        $validationResult = MyAssignTaskValidations::validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        if ($this->checkTaskDateExist()) {
            return response([
                'success' => false,
                'message' => $request->task_date . ' task date already exist.'
            ]);
        }

        DB::beginTransaction();

        try {
            $requestAll = $request->all();
            $requestAll['farmer_user_id'] = (int)user_id();
            $requestAll['task_status'] = 2;
            $requestAll['created_by'] = (int)user_id();
            $requestAll['updated_by'] = (int)user_id();

            $myAssignTask = MyAssignTask::find($request->id);

            if ($myAssignTask) {
                $myAssignTask->update($requestAll);

                save_log([
                    'data_id'    => $myAssignTask->id,
                    'table_name' => 'my_assign_tasks'
                ]);

                DB::commit();
            } else {
                return response([
                    'success' => false,
                    'message' => 'Failed to save data.',
                    'errors'  => env('APP_ENV') !== 'production' ? 'No existing data found' : []
                ]);
            }
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
            'data'    => $myAssignTask
        ]);
    }

    /**
     * My Assign Task update
     */
    public function update(Request $request, $id)
    {
        $validationResult = MyAssignTaskValidations::validate($request, $id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $myAssignTask = MyAssignTask::find($id);

        if (!$myAssignTask) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        if ($this->checkTaskDateExist()) {
            return response([
                'success' => false,
                'message' => $request->task_date . ' task date already exist.'
            ]);
        }

        try {
            $requestAll = $request->except(['farmer_user_id']);
            $requestAll['updated_by'] = (int)user_id();
            $requestAll['task_status'] = 2;
            $myAssignTask->fill($requestAll);
            $myAssignTask->save();

            save_log([
                'data_id'       => $myAssignTask->id,
                'table_name'    => 'my_assign_tasks',
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
            'data'    => $myAssignTask
        ]);
    }

    /**
     * My Assign Task status update
     */
    public function toggleStatus($id)
    {
        $myAssignTask = MyAssignTask::find($id);

        if (!$myAssignTask) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $myAssignTask->status = $myAssignTask->status ? 0 : 1;
        $myAssignTask->update();

        save_log([
            'data_id'       => $myAssignTask->id,
            'table_name'    => 'my_assign_tasks',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $myAssignTask
        ]);
    }

    /**
     * My Assign Task destroy
     */
    public function destroy($id)
    {
        $myAssignTask = MyAssignTask::find($id);

        if (!$myAssignTask) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $myAssignTask->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'master_loan_rates',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    /**
     * Check whether task date already exist
     */
    private function checkTaskDateExist()
    {
        return MyAssignTask::whereFarmerUserId(user_id())
            ->whereTaskDate(request('task_date'))
            ->exists();
    }

    protected function checkDailyTask() {
        if (DB::table('pump_operators')
            ->where('daily_task_entry_required', 1)
            ->where('pump_operator_user_id', user_id())
            ->exists()) {
            if (!MyAssignTask::where('farmer_user_id', user_id())->whereDate('created_at', Carbon::today())->exists()) {
                MyAssignTask::create([
                    'farmer_user_id' => user_id()
                ]);
            }
        }
    }
    // protected function checkDailyTask() {
    //     if (DB::table('my_assign_tasks')
    //         ->join('pump_operators', 'my_assign_tasks.farmer_user_id', '=', 'pump_operators.pump_operator_user_id')
    //         ->where('my_assign_tasks.status', 0)
    //         ->where('my_assign_tasks.farmer_user_id', user_id())
    //         ->exists()) {
    //         if (!MyAssignTask::where('farmer_user_id', user_id())->whereDate('created_at', Carbon::today())->exists()) {
    //             MyAssignTask::create([
    //                 'farmer_user_id' => user_id()
    //             ]);
    //         }
    //     }
    // }
}
