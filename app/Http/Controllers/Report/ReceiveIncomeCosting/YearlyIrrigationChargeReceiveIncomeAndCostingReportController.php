<?php

namespace App\Http\Controllers\Report\ReceiveIncomeCosting;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\PumpInstallation\DeepTubewellYearlyFinalReport;

class YearlyIrrigationChargeReceiveIncomeAndCostingReportController extends Controller
{
    /**
     * get all Yearly irrigation charge receive, income and costing report
     */
    public function index(Request $request)
    {  
        $query = DB::table('deep_tubewell_yearly_final_report')
                    ->select('deep_tubewell_yearly_final_report.*');
        
        if($request->org_id){
            $query = $query->where('org_id', $request->org_id);
        }

        if($request->report_type_id){
            $query = $query->where('report_type_id', $request->report_type_id);
        }
        
        
        if($request->pump_id){
            $query = $query->where('pump_id', $request->pump_id);
        }
        
        if($request->division_id){
            $query = $query->where('division_id', $request->division_id);
        }
        
        if($request->district_id){
            $query = $query->where('district_id', $request->district_id);
        }
        
        if($request->upazilla_id){
            $query = $query->where('upazilla_id', $request->upazilla_id);
        }
        
        if($request->union_id){
            $query = $query->where('union_id', $request->union_id);
        }

        if ($request->from_date && $request->to_date) 
		{
		    $startDate   = date('Y-m-d', strtotime($request->from_date));
		    $endDate     = date('Y-m-d', strtotime($request->to_date));
		    $query       = $query->whereBetween('created_at', [$startDate, $endDate]);
		}

		if (isset($request->from_date) && !isset($request->to_date))
		{
		    $query = $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
		}

		if (!isset($request->from_date) && isset($request->to_date))
		{
		    $query = $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
		}

        $list = $query->get();

        if(count($list) > 0)
        {
	        return response([
	            'success' => true,
	            'message' => 'Yearly irrigation charge receive, income and costing report',
	            'data' => $list
	        ]);
        }
        else
        {
        	return response([
	            'success' => false,
	            'message' => 'Data not found !!'
	        ]);
        }
    }


    public function show($id)
    {
        $query = DeepTubewellYearlyFinalReport::find($id);

        if(!$query){
            return response([
                'success' => true,
                'message' => "Data Not found"
            ]);
        }

        return response([
                'success' => true,
                'message' => "Yearly irrigation charge receive, income and costing report details",
                'data' => $query
            ]);
       
    }
}
