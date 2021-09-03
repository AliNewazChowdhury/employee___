<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;

class MyAssignTask extends Model
{
	protected $table="my_assign_tasks";

    protected $fillable =[
		'org_id',
		'farmer_user_id',
		'meter_reading_before',
		'meter_reading_after',
		'pump_running_time',
		'irrigation_area',
		'task_date',
		'task_status',
		'working_date',
		'created_by',
		'updated_by',
		'status'
    ];
}
