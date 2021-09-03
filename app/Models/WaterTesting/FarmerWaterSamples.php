<?php
namespace App\Models\WaterTesting;

use Illuminate\Database\Eloquent\Model;

class FarmerWaterSamples extends Model
{
    protected $table="far_water_samples";

    protected $fillable =[
        'far_water_test_apps_id',  
        'laboratory_id',
		'note',
		'note_bn',	
		'created_at' 
    ];


    public function far_water_application(){
		 return $this->belongsTo(FarmerWaterTestApplication::class, 'far_water_test_apps_id','id');
	}
}

