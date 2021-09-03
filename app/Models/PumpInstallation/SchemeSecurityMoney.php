<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeSecurityMoney extends Model
{
    protected $table ="scheme_security_money";

    protected $fillable =[
        'scheme_application_id','org_id','pump_type_id','discharge_cusec','amount','payment_type'
    ];
}
