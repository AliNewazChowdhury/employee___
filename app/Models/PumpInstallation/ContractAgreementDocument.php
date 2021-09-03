<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class ContractAgreementDocument extends Model
{
    protected $table ="far_scheme_agreemt_doc";

    protected $fillable =[
        'agreement_date','attachment','scheme_application_id'
    ];
}
