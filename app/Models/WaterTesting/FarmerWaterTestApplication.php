<?php
namespace App\Models\WaterTesting;

use Illuminate\Database\Eloquent\Model;
use App\Models\Payment\IrrigationPayment;

class FarmerWaterTestApplication extends Model
{
    protected $table ="far_water_test_apps";

    protected $fillable =[
        'org_id',  
		'farmer_id',   
		'email',   
		'mobile_no',   
		'name',    
		'name_bn', 
		'sample_number',   
		'sample_serial',   
		'testing_type_id', 
		'far_division_id', 
		'far_district_id', 
		'far_upazilla_id', 
		'far_union_id',    
		'far_village', 
		'far_village_bn',  
		'from_date',   
		'to_date', 
		'status',
		'application_id',
        'payment_status',
        'water_testing_parameter_id',
		'id_serial' 
    ];

    public function waterTestSamples()
    {
        return $this->hasOne(FarmerWaterSamples::class, 'far_water_test_apps_id');
    }
    public function waterTestReports()
    {
        return $this->hasOne(FarmerWaterTestReports::class, 'far_water_test_apps_id');
    }

    public function waterTestRejects()
    {
        return $this->hasOne(FarmerWaterTestRejects::class, 'far_water_test_apps_id');
    }
    public function payment()
    {
        return $this->belongsTo(IrrigationPayment::class, 'id', 'far_application_id');
    }

}
