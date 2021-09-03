<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class FarSchemeLicense extends Model
{
    protected $table ="far_scheme_license";

    protected $fillable =[
        'scheme_application_id','license_no','issue_date','attachment','is_verified'
    ];
}
