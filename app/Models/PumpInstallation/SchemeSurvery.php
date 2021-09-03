<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeSurvery extends Model
{
    protected $table ="far_scheme_surveys";

    protected $fillable =[
        'scheme_application_id','survey_date','suggestion'
    ];
}
