<?php

namespace App\Models\PumpInfoManagement;

use Illuminate\Database\Eloquent\Model;

class PumpScheduler extends Model
{
    protected $table = "pump_schedulers";

    protected $fillable = [
       'org_id','pump_id','ontime','offtime'
    ];
}
