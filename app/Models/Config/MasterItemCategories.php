<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterItemCategories extends Model
{
    protected $table="master_item_categories";

    protected $fillable =[
        'org_id',  
		'category_name',   
		'category_name_bn',    
		'status'
    ];
}
