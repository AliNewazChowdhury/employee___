<?php

namespace App\Models\TaskManagement;

use Illuminate\Database\Eloquent\Model;

class TaskReviewNotes extends Model
{
    protected $table="task_review_notes";

    protected $fillable =[ 
		'task_id',	
		'note',	
		'note_bn',	
		'created_by',	
		'updated_by',
		'status'
    ];
}
