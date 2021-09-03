<?php

namespace App\Models\TaskManagement;

use App\Models\Notification;
use Illuminate\Database\Eloquent\Model;


class TaskAssignTasks extends Model
{
   protected $table="task_assign_tasks";

    protected $fillable =[
		'org_id',
		'task_name',
		'task_name_bn',
		'task_type_id',
		'is_verified',
		'assign_user_id',
		'note',
		'note_bn',
		'attachment',
		'created_by',
		'updated_by',
		'status'
    ];
    public function task_reports()
    {
        return $this->hasMany('App\Models\TaskManagement\TaskTaskReports', 'task_id');
    }

    public function notifications()
    {
        return $this->morphMany(Notification::class, 'notification');
    }
}
