<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterLaboratory extends Model
{
    protected $table="master_laboratories";

    protected $fillable = [
        'laboratory_name','laboratory_name_bn','address','address_bn','org_id','division_id','district_id','upazilla_id','status'
    ];
}
