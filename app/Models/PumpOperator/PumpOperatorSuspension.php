<?php

namespace App\Models\PumpOperator;

use Illuminate\Database\Eloquent\Model;

class PumpOperatorSuspension extends Model
{
    protected $table = "pump_opt_suspensions";
							
    protected $fillable = [
        'org_id',
        'pump_id',
        'operator_id',
        'reason',
        'reason_bn',
        'message',
        'message_bn',
        'suspend_date'
    ];
}
