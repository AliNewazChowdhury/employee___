<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterPayment extends Model
{
    protected $table ="master_payments";

    protected $fillable = [
    'org_id',
    'application_type_id',
    'payment_type_id',
    'scheme_type_id',
    'participation_category_id',
    'pump_type_id',
    'discharge_cusec',
    'circle_area_id',
    'testing_parameter_id',
    'gender',
    'amount',
    'effective_from',
    'created_by',
    'updated_by',
    'status'
    ];
}
