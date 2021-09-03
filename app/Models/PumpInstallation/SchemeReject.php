<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeReject extends Model
{
    protected $table ="far_scheme_rejects";

    protected $fillable =[
        'scheme_application_id','reject_note'
    ];
}
