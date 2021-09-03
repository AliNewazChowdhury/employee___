<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeNote extends Model
{
    protected $table ="far_scheme_notes";

    protected $fillable =[
        'scheme_application_id','note'
    ];
}
