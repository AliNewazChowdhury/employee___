<?php

namespace App\Http\Controllers\Report\PaymentCollection;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PaymentCollectionReportController extends Controller
{
     public function index(Request $request)
    { 
		
    	if($request->application_type_id){
	    	$query = DB::table('irrigation_payments');    	
	      	if($request->application_type_id && $request->application_type_id == 1){
	      		
	      		$query = $query->join('far_scheme_application','irrigation_payments.far_application_id','far_scheme_application.id')
	      				->join('far_basic_infos','irrigation_payments.farmer_id','far_basic_infos.farmer_id')
						->select('irrigation_payments.*',
								'far_basic_infos.id as far_basic_infos_id',
								'far_basic_infos.name as far_name',
								'far_basic_infos.name_bn as far_name_bn');

				if ($request->application_id){
					$query = $query->where('far_scheme_application.application_id', $request->application_id);	
				}
			}

			if($request->application_type_id && $request->application_type_id == 2){
				
	      		$query = $query->join('far_pump_opt_apps','irrigation_payments.far_application_id','far_pump_opt_apps.id')      				 
						->join('far_basic_infos','irrigation_payments.farmer_id','far_basic_infos.farmer_id')
						->select('irrigation_payments.*',
								'far_basic_infos.id as far_basic_infos_id',
								'far_basic_infos.name as far_name',
								'far_basic_infos.name_bn as far_name_bn'
							);
				if ($request->application_id)
				{					
					$query = $query->where('far_pump_opt_apps.application_id', $request->application_id);		
				}
				//return ($query->get()); 
			}

			if($request->application_type_id && $request->application_type_id == 3){
	      		$query = $query->join('far_smart_card_apps','irrigation_payments.far_application_id','far_smart_card_apps.id')      				 
						->join('far_basic_infos','irrigation_payments.farmer_id','far_basic_infos.farmer_id')
						->select('irrigation_payments.*',
								'far_basic_infos.id as far_basic_infos_id',
								'far_basic_infos.name as far_name',
								'far_basic_infos.name_bn as far_name_bn'
							);
				if ($request->application_id)
				{					
					$query = $query->where('far_smart_card_apps.application_id', $request->application_id);
				}		
			}

			if($request->application_type_id && $request->application_type_id == 4){
	      		$query = $query->join('far_water_test_apps','irrigation_payments.far_application_id','far_water_test_apps.id')
	      			->join('far_basic_infos','irrigation_payments.farmer_id','far_basic_infos.farmer_id')
						->select('irrigation_payments.*');
				if ($request->application_id)
				{							
					$query = $query->where('far_water_test_apps.irrigation_payments', $request->application_id);	
				}	
			}

			if($request->application_type_id){
	      		$query = $query->where('irrigation_payments.application_type', $request->application_type_id);
			}

	        if ($request->org_id) {
	            $query = $query->where('irrigation_payments.org_id', $request->org_id);
	        }
	        
	        if ($request->payment_type_id) {
	            $query = $query->where('irrigation_payments.payment_type_id', $request->payment_type_id);
	        }

	        if ($request->transaction_no) {
	            $query = $query->where('irrigation_payments.transaction_no', $request->transaction_no);
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

	        $query = $query->orderBy('id', 'DESC')->get();

	        if(count($query) > 0){
	        	return response([
		            'success' => true,
		            'message' => 'Farmer payment collection report',
		            'data' => $query
		        ]);
	        }
	        else
	        {
	        	return response([
		            'success' => false,
		            'message' => 'Data not found'
		        ]);
	        }  
        }else{
        	return response([
	            'success' => false,
	            'message' => 'Please select application type'
	        ]);	
        }      
    }
}
 