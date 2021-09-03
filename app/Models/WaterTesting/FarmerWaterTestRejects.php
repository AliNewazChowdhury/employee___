<?php
namespace App\Models\WaterTesting;

use Illuminate\Database\Eloquent\Model;

class FarmerWaterTestRejects extends Model
{
   	protected $table="far_water_rejects";

    protected $fillable =[
        'far_water_test_apps_id',
		'note',
		'note_bn',	
		'created_at' 
    ];


    public function far_water_application(){
		 return $this->belongsTo(FarmerWaterTestApplication::class, 'far_water_test_apps_id','id');
	}
}
