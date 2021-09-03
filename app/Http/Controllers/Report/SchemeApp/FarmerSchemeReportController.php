<?php

namespace App\Http\Controllers\Report\SchemeApp;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class FarmerSchemeReportController extends Controller
{
    /**
    * get all farmer scheme
    */
    public function index(Request $request)
    {        
        $query = DB::table('far_scheme_application')                          
                        ->leftjoin('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->leftjoin('far_scheme_surveys','far_scheme_application.id', '=','far_scheme_surveys.scheme_application_id') 
                        ->select('far_scheme_application.id',	
								'far_scheme_application.application_id',
								'far_scheme_application.name',	
								'far_scheme_application.name_bn',
								'far_scheme_application.far_mobile_no',	
								'far_scheme_application.status',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_surveys.id as scheme_survey_id',
                                'far_scheme_application.nid',
                                'far_scheme_application.org_id',
                                'far_scheme_application.scheme_type_id',
                                'far_scheme_application.far_division_id as division_id',
                                'far_scheme_application.far_district_id as district_id',
                                'far_scheme_application.far_upazilla_id as upazilla_id',
                                'far_scheme_application.far_union_id as union_id',
                                'far_scheme_application.far_village',
                                'far_scheme_application.far_village_bn',
                                );
     	
     	if ($request->status) {
            $query = $query->where('far_scheme_application.status', $request->status);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        } 

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }
        
        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }  

        if ($request->pump_mauza_no) {
            $query = $query->where('far_scheme_app_details.pump_mauza_no', 'like', "{$request->pump_mauza_no}%")
                            ->orWhere('far_scheme_app_details.pump_mauza_no_bn', 'like', "{$request->pump_mauza_no}%");
        }  

        if ($request->from_date && $request->to_date) 
        {
            $startDate   = date('Y-m-d', strtotime($request->from_date));
            $endDate     = date('Y-m-d', strtotime($request->to_date));
            $query       = $query->whereBetween('far_scheme_application.created_at', [$startDate, $endDate]);
        }
        
        if ($request->from_date && !isset($request->to_date))
        {
            $query = $query->whereDate('far_scheme_application.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        
        if (!isset($request->from_date) && $request->to_date)
        {
            $query = $query->whereDate('far_scheme_application.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }        

        $list = $query->get();
        return response([
            'success' => true,
            'message' => 'Farmer scheme application report',
            'data' => $list
        ]);
    }
}
