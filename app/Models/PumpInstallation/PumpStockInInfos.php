<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class PumpStockInInfos extends Model
{
    protected $table="pump_stock_in_infos";

    protected $fillable =[
        'stock_in_id',  
		'org_id',
		'office_id',
		'id_serial',
		'stock_date',
		'created_by',	
		'updated_by',   
		'status',
		'created_at',
		'updated_at',
		'division_id',
		'district_id',
		'upazilla_id',
		'union_id'
    ];
}
