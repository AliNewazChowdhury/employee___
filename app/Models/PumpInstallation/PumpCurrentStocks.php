<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class PumpCurrentStocks extends Model
{
    protected $table="pump_current_stocks";

    protected $fillable =[
        'org_id',  
        'office_id',  
		'item_id',
		'quantity',
		'created_by',	
		'updated_by' 
    ];
}
