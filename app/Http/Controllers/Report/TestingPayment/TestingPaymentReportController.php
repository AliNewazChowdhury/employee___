<?php

namespace App\Http\Controllers\Report\TestingPayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Payment\IrrigationPayment;

class TestingPaymentReportController extends Controller
{
     public function index(Request $request)
    {        
        $query = IrrigationPayment::with('farmerBasicInfos');
         
        /*dd($query);
        $query = DB::table('irrigation_payments') 
                        ->leftJoin('far_water_test_apps','irrigation_payments.farmer_id', '=','far_water_test_apps.farmer_id')
                        ->leftJoin('master_laboratories','irrigation_payments.org_id', '=','master_laboratories.org_id')
                        ->select('irrigation_payments.*',
                                'far_water_test_apps.name',
                                'far_water_test_apps.name_bn',
                                'far_water_test_apps.testing_type_id',
                                'far_water_test_apps.far_division_id',
								'far_water_test_apps.far_district_id',
								'far_water_test_apps.far_upazilla_id',
								'far_water_test_apps.far_union_id',
								'far_water_test_apps.far_village',
								'far_water_test_apps.far_village_bn',
                                'master_laboratories.laboratory_name', 
								'master_laboratories.laboratory_name_bn'
                                );*/

       /*  $query = DB::select('select irrigation_payments.*, far_water_test_apps.name, far_water_test_apps.name_bn, far_water_test_apps.testing_type_id, far_water_test_apps.far_division_id, far_water_test_apps.far_district_id, far_water_test_apps.far_upazilla_id, far_water_test_apps.far_union_id, far_water_test_apps.far_village, far_water_test_apps.far_village_bn, master_laboratories.laboratory_name, master_laboratories.laboratory_name_bn FROM irrigation_payments join far_water_test_apps ON irrigation_payments.farmer_id = far_water_test_apps.farmer_id join master_laboratories ON irrigation_payments.org_id = master_laboratories.org_id where irrigation_payments.application_type = 4');*/




		$query = $query->where('irrigation_payments.application_type', 4);

        if ($request->org_id) {
            $query = $query->where('irrigation_payments.org_id', $request->org_id);
        } 

        if ($request->from_date && $request->to_date) 
        {
            $startDate   = date('Y-m-d', strtotime($request->from_date));
            $endDate     = date('Y-m-d', strtotime($request->to_date));
            $query       = $query->whereBetween('irrigation_payments.created_at', [$startDate, $endDate]);
        }
        
        if ($request->from_date && !isset($request->to_date))
        {
            $query = $query->whereDate('irrigation_payments.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        
        if (!isset($request->from_date) && $request->to_date)
        {
            $query = $query->whereDate('irrigation_payments.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }        

        $list = $query->get();
       /* $list = $query;*/

        if(count($list) > 0){
        	return response([
	            'success' => true,
	            'message' => 'Water testing payment report',
	            'data' => $list
	        ]);
        }else{
        	return response([
	            'success' => true,
	            'message' => 'Data not found!!'
	        ]);
        }        
    }
}
