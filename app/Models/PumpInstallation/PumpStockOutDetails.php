<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class PumpStockOutDetails extends Model
{
    protected $table="pump_stock_out_details";

    protected $fillable =[
        'stock_out_infos_id',  
		'item_id',
		'quantity'
    ];
}
