<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class pumpStockOutInfos extends Model
{
    protected $table="pump_stock_out_infos";

    protected $fillable =[
        'stock_out_id',  
		'org_id',
		'office_id',
		'id_serial',
		'stock_out_date',
		'reason',
		'reason_bn',
		'purpose',
		'purpose_bn',
		'remarks',
		'remarks_bn',
		'created_by',	
		'updated_by',   
		'status',
		'created_at'
    ];
}


