<?php

namespace App\Models\WaterTesting;

use Illuminate\Database\Eloquent\Model;

class FarmerWaterTestReports extends Model
{
    protected $table="far_water_test_reports";

    protected $fillable =[
        'far_water_test_apps_id',
		'memo_no',
		'attachment',	
		'created_at' 
    ];


    public function far_water_application(){
		 return $this->belongsTo(FarmerWaterTestApplication::class, 'far_water_test_apps_id','id');
	}
}
