<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;

class FarRefundDeduct extends Model
{
    protected $table = 'far_app_payment_refunds_deducts';
    protected $guarded = [];
}
