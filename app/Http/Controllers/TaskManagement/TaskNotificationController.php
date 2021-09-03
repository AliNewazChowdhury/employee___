<?php

namespace App\Http\Controllers\TaskManagement;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class TaskNotificationController extends Controller
{
    public function index () {
        $query = DB::table('my_assign_tasks')
            ->leftJoin('pump_operators', 'my_assign_tasks.farmer_user_id', '=', 'pump_operators.pump_operator_user_id')
            ->where('my_assign_tasks.status', 0)
            ->whereDate('my_assign_tasks.created_at', '<' , Carbon::now()->subDays(7))
            ->select(
                'my_assign_tasks.*',
                'my_assign_tasks.id as task_id',
                'my_assign_tasks.created_at as task_created_at',
                'pump_operators.*'
            )
            ->orWhereNull('my_assign_tasks.id');

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Task Notification list',
            'data' => $list
        ]);
    }

/*$query = DB::table('task_assign_tasks')
->leftJoin('task_task_reports', 'task_assign_tasks.id', '=', 'task_task_reports.task_id')
->where('task_assign_tasks.status', 0)
->whereDate('task_assign_tasks.created_at', '<' , Carbon::now()->subDays(7))
->select(
'task_assign_tasks.*',
'task_task_reports.id as task_report_id',
'task_task_reports.task_id',
'task_task_reports.created_at as task_task_reports_created_at'
)
->whereNull('task_task_reports.task_id')
->orWhereRaw('task_task_reports.created_at + interval -7 day > task_assign_tasks.created_at');

$list = $query->paginate(request('per_page', config('app.per_page')));

return response([
'success' => true,
'message' => 'Task Notification list',
'data' => $list
]);*/
    /* protected function checkNotification () {
        DB::beginTransaction();
        try {
            $query = DB::table('task_assign_tasks')
                ->leftJoin('task_task_reports', 'task_assign_tasks.id', '=', 'task_task_reports.task_id')
                ->where('task_assign_tasks.status', 0)
                ->whereDate('task_assign_tasks.created_at', '<' , Carbon::now()->subDays(7))
                ->select(
                    'task_assign_tasks.*',
                    'task_task_reports.id as task_report_id',
                    'task_task_reports.task_id',
                    'task_task_reports.created_at as task_task_reports_created_at'
                )
                ->whereNull('task_task_reports.task_id')
                ->orWhereRaw('task_task_reports.created_at + interval -7 day > task_assign_tasks.created_at');
            $taskReportDelays = $query->get();

            $notificationBag = [];
            foreach ($taskReportDelays as $taskReportDelay) {
                $notification = [
                    'body' => json_encode([
                        'description' => 'Task has not been reported yet.',
                        'task_name' => $taskReportDelay->task_name,
                        'task_name_bn' => $taskReportDelay->task_name_bn,
                        'assign_username' => $taskReportDelay->assign_username,
                        'id' => $taskReportDelay->id
                    ]),
                    'notification_id' => $taskReportDelay->id,
                    'notification_type' => TaskAssignTasks::class,
                    'posted_by' => user_id() ?? 0,
                    'posted_for' => 1
                ];
                array_push($notificationBag, $notification);

                if (!Notification::where('notification_id', $taskReportDelay->id)
                    ->where('notification_type', TaskAssignTasks::class)
                    ->exists()) {
                    $this->notification->updateOrInsert($notification,
                        ['notification_id' => $taskReportDelay->id,
                            'notification_type' => TaskAssignTasks::class]);
                }
            }
            DB::commit();

        } catch (Exception $e) {
            DB::rollBack();

            return false;
        }

       return true;
    }*/
}
