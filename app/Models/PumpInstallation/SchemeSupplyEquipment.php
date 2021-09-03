<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeSupplyEquipment extends Model
{
    protected $table ="far_scheme_supply_equipments";

    protected $fillable =[
        'supply_note','supply_date','requisition_id'
    ];
}
