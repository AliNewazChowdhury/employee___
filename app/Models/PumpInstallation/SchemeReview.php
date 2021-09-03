<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeReview extends Model
{
    protected $table ="far_scheme_reviews";

    protected $fillable =[
        'scheme_application_id','review_note'
    ];
}
