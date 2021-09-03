<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class FarSchemeDocs extends Model
{
    protected $table ="far_scheme_docs";

    protected $fillable = [
        'scheme_application_id','user_id','document_title','document_title_bn','attachment'
    ];
}
