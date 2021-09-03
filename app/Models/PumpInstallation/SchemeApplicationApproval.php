<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeApplicationApproval extends Model
{
    protected $table ="far_scheme_approvals";

    protected $fillable =[
        'scheme_application_id', 'sender_id', 'receiver_id', 'note', 'note_bn'
    ];
}
