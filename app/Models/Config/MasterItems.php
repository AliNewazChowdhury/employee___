<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterItems extends Model
{
    protected $table="master_items";

    protected $fillable =[
        'org_id',
		'category_id',
		'sub_category_id',
		'measurement_unit_id',
		'item_name',
		'item_name_bn',
        'specification',
        'specification_bn',
		'item_code',
		'created_by',
		'updated_by',
		'status'
    ];
}
