<?php

namespace App\Models\PumpOperator;

use Illuminate\Database\Eloquent\Model;

class PumpOptAppsSurvey extends Model
{
    protected $table = "pump_opt_apps_surveys";

    protected $fillable = [
        'pump_opt_apps_id', 'user_id', 'survey_date', 'note'
    ];
}
