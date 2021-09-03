<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterHorsePower extends Model
{
    protected $table = "master_horse_powers";

    protected $fillable = [
        'org_id','horse_power','pump_type_id','status'
    ];
}
