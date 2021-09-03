<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class ContractAgreement extends Model
{
    protected $table ="far_scheme_agreement";

    protected $fillable =[
        'agreement_details','agreement_details_bn','scheme_application_id'
    ];
}
