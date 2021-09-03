<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class PumpStockInDetails extends Model
{
    protected $table="pump_stock_in_details";

    protected $fillable =[
        'stock_in_infos_id',  
		'item_id',
		'quantity'
    ];
}
