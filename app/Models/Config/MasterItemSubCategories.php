<?php

namespace App\Models\Config;

use Illuminate\Database\Eloquent\Model;

class MasterItemSubCategories extends Model
{
   protected $table="master_item_sub_categories";

    protected $fillable =[
        'org_id',  
        'category_id',  
		'sub_category_name',   
		'sub_category_name_bn',    
		'status'
    ];
}



