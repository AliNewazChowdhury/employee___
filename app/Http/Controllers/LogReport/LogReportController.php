<?php

namespace App\Http\Controllers\LogReport;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\LogReport\LogReport;
class LogReportController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }  

    public function index(Request $request)
    {
        $query = LogReport::latest();
        if ($request->execution_type == 0) {
            $query = $query->where('execution_type',$request->execution_type);
        }
        if (!empty($request->execution_type)) {
            if ($request->execution_type != '-1'){
                $query = $query->where('execution_type',$request->execution_type);   
            }
        }
        if ($request->user_id) {
            $query = $query->where('user_id',$request->user_id);
        }
        if ($request->from_date) {
            $query = $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        if ($request->to_date) {
            $query = $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }
        $LogReport = $query->get();

        return response([
            'success' => true,
            'message' => 'LogReport list',
            'data' =>$LogReport
        ]);
    }
}
