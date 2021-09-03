<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeRequistionApproval extends Model
{
    protected $table = "far_requisition_approvals";
    
    protected $fillable =[
        'scheme_application_id','requisition_id','sender_id','receiver_id','note'
    ];
}
