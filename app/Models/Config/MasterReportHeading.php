<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterReportHeading extends Model
{
    protected $table ="master_report_headers";

    protected $fillable = [
        'heading','heading_bn','left_logo','right_logo','address','address_bn','org_id'
    ];
}
