<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterContractor extends Model
{
    protected $table ="master_contractors";

    protected $fillable =[
        'contractor_name','contractor_name_bn','phone_no','address','address_bn','org_id','	status'
    ];
}
