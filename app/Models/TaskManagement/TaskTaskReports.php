<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;

class TaskTaskReports extends Model
{
    protected $table="task_task_reports";

    protected $fillable =[ 
		'task_id',	
		'complete_type_id',	
		'attachment',	
		'note',	
		'note_bn',	
		'task_date',	
		'created_by',	
		'updated_by',
		'status'
    ];
}
