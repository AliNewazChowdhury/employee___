<?php

namespace App\Models\PumpInstallation;

use Illuminate\Database\Eloquent\Model;

class SchemeRequisitionDetails extends Model
{
    protected $table = "far_scheme_req_details";
    
    protected $fillable =[
        'requisition_id','item_id','quantity'
    ];
}
