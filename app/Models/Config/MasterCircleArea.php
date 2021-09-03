<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterCircleArea extends Model
{
    protected $table = "master_circle_areas";

    protected $fillable = [
       'org_id','division_id','district_id','circle_area_name', 'circle_area_name_bn', 'status'
    ];
}
