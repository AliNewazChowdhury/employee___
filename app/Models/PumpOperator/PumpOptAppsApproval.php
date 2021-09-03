<?php

namespace App\Models\PumpOperator;

use Illuminate\Database\Eloquent\Model;

class PumpOptAppsApproval extends Model
{
    protected $table = "pum_opt_apps_approvals";

    protected $fillable = [
        'pump_opt_apps_id', 'sender_id', 'sender_id', 'note'
    ];
}
