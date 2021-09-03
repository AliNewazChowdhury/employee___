<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterPaymentType extends Model
{
    protected $table ="master_payment_types";

    protected $fillable = [
        'type_name','type_name_bn','amount','org_id','status'
    ];
}
