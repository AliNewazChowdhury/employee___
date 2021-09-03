<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterSchemeForm extends Model
{
    protected $table ="master_scheme_affidavits";

    protected $fillable =[
        'affidavit','affidavit_bn','scheme_type_id','org_id','status'
    ];
}
