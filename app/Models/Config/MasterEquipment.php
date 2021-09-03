<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterEquipment extends Model
{
    protected $table="master_equipment_types";

    protected $fillable =[
        'eq_type_name','eq_type_name_bn','org_id','status'
    ];
}
