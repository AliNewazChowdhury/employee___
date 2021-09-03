<?php

namespace App\Http\Controllers\PumpMaintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class ReportController extends Controller
{
    /**
     * get farmer list
     */
    public function farmerList (Request $request) 
    {   
        $list = DB::table('far_basic_infos')->select('*')->get();

        return response([
            'success'   => true,
            'message'   => 'Farmer list',
            'data'      => $list
        ]);

    }

    /**
     * show complain report data
     */
    public function complainReport (Request $request) 
    {   
        $query = DB::table('far_complains')->select('far_complains.*')->orderBy('id','DESC');

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->status && $request->status != 100) {
            $query = $query->where('status', $request->status);
        }

        if ($request->status && $request->status == 100) { 
            $query = $query->where('status', '>=', 2)->where('status', '!=', 7);
        }

        if ($request->from_date) {
            $query = $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->to_date) {
            $query = $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->to_date)));
        }

        $list = $query->get();

        return response([
            'success'   => true,
            'message'   => 'Farmer complain report',
            'data'      => $list
        ]);

    }

    /**
     * show maintenance report data
     */
    public function maintenanceReport (Request $request) 
    {   
        $query = DB::table('far_complains')
                    ->leftjoin('far_complain_tro_equipments','far_complains.id','far_complain_tro_equipments.complain_id')
                    ->select('far_complains.*', 'far_complain_tro_equipments.id as tro_equipment_id')
                    ->where('far_complains.status', 4)
                    ->orderBy('far_complains.id','DESC');
                    
        if ($request->org_id) {
            $query = $query->where('far_complains.org_id', $request->org_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_complains.far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_complains.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_complains.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_complains.far_union_id', $request->far_union_id);
        }

        if ($request->status && $request->status != 100) {
            $query = $query->where('far_complains.status', $request->status);
        }

        if ($request->status && $request->status == 100) { 
            $query = $query->where('far_complains.status', '>=', 2)->where('status', '!=', 7);
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_complains.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_complains.created_at', '>=', date('Y-m-d', strtotime($request->to_date)));
        }

        $list = $query->get();

        return response([
            'success'   => true,
            'message'   => 'Maintenance report',
            'data'      => $list
        ]);

    }

    /**
     * rating feedback report
     */
    public function ratingFeedbackReport (Request $request) 
    {   
        $query = DB::table('far_ratings')
                    ->leftjoin('far_basic_infos', 'far_ratings.farmer_id', 'far_basic_infos.farmer_id')
                    ->select('far_ratings.*',
                        'far_basic_infos.name as farmer_name',
                        'far_basic_infos.name_bn as farmer_name_bn'
                    );

        if ($request->feedback) {
            $query = $query->where('far_ratings.feedback', 'like', "%{$request->feedback}%")
                           ->orWhere('far_ratings.feedback_bn', 'like', "%{$request->feedback}%");
        }

        if ($request->rating) {
            $query = $query->where('far_ratings.rating', $request->rating);
        } 

        if ($request->org_id) {
            $query = $query->where('far_ratings.org_id', $request->org_id);
        }

        if ($request->division_id && $request->division_id != 0) {
            $query = $query->where('far_ratings.division_id', $request->division_id);
        }
         
        if ($request->district_id && $request->district_id != 0) {
            $query = $query->where('far_ratings.district_id', $request->district_id);
        }
         
        if ($request->upazilla_id && $request->upazilla_id != 0) {
            $query = $query->where('far_ratings.upazilla_id', $request->upazilla_id);
        }    
        
        if ($request->from_date) {
            $query = $query->whereDate('far_ratings.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_ratings.created_at', '>=', date('Y-m-d', strtotime($request->to_date)));
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => "Farmers ratings list",
            'data'    => $list
        ]);
    }
}
