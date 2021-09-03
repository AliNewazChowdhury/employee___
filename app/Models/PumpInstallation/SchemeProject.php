<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeProject extends Model
{
    protected $table ="far_scheme_projects";

    protected $fillable =[
        'scheme_application_id','project_id'
    ];
}
