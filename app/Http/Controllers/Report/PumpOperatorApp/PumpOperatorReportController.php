<?php

namespace App\Http\Controllers\Report\PumpOperatorApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PumpOperatorReportController extends Controller
{
    /**
    * get all pump operator
    */
    public function index(Request $request)
    {
        $query = DB::table('far_pump_opt_apps')
					->select('id',
						'org_id',
						'farmer_id',
						'name',
						'name_bn',
						'far_division_id',
						'far_district_id',
						'far_upazilla_id',
						'far_union_id',
						'far_village',
						'far_village_bn',
						'far_mobile_no',
						'payment_status',
						'application_id',
                        'email',
                        'father_name',
                        'father_name_bn',
                        'mother_name',
                        'mother_name_bn',
                        'nid',
                        'qualification',
						'status'
						);



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

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

      	if ($request->from_date && $request->to_date)
        {
            $startDate   = date('Y-m-d', strtotime($request->from_date));
            $endDate     = date('Y-m-d', strtotime($request->to_date));
            $query       = $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->from_date && !isset($request->to_date))
        {
            $query = $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if (!isset($request->from_date) && $request->to_date)
        {
            $query = $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        $list = $query->get();
        return response([
            'success' => true,
            'message' => 'Farmer pump operator report',
            'data' => $list
        ]);
    }
}
