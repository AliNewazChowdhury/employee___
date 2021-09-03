<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterScheme extends Model
{
    protected $table ="master_scheme_types";

    protected $fillable = [
        'scheme_type_name','scheme_type_name_bn','org_id','status'
    ];
}
