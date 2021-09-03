<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeParticipationFee extends Model
{
    protected $table ="scheme_participation_fees";

    protected $fillable =[
        'scheme_application_id','org_id','participation_category_id','discharge_cusec','amount','payment_type','circle_area_id'
    ];
}
