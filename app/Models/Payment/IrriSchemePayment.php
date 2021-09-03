<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class IrriSchemePayment extends Model
{
    protected $table = 'irri_scheme_payments';
    protected $fillable = [
        'master_payment_id','farmer_id','org_id','scheme_application_id','payment_type_id','scheme_type_id',
        'scheme_participation_fee_id','scheme_security_money_id',
        'amount','trnx_currency','mac_addr','transaction_no','status','pay_status','circle_area_id'
    ];

}