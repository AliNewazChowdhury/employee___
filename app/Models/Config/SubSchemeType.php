<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class SubSchemeType extends Model
{
    protected $table = "sub_scheme_types";

    protected $fillable =[
        'sub_scheme_type_name',	
        'sub_scheme_type_name_bn',	
		'org_id',
		'master_scheme_type_id',
		'status',
		'created_by',
		'updated_by'
    ];
}
