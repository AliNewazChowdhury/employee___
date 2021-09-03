<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterProject extends Model
{
    protected $table="master_projects";

    protected $fillable = [
        'project_name','project_name_bn','org_id','status'
    ];
}
