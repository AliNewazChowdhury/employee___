<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Models\PumpInstallation\PumpCurrentStocks;
use Illuminate\Http\Request;
use DB;

class ReportController extends Controller
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

    /**
     * get all Master Item Categories
     */
    public function storeReport(Request $request){
        $query = PumpCurrentStocks::
            leftjoin('master_items','master_items.id','pump_current_stocks.item_id')
            ->leftjoin('master_item_categories','master_items.category_id', '=','master_item_categories.id')
            ->select('pump_current_stocks.*','master_items.id as item_id','master_items.item_name','master_items.item_name_bn','master_items.category_id','master_item_categories.id as master_item_category_id','master_item_categories.category_name','master_item_categories.category_name_bn');
        if (!(empty($request->org_id))) {
            $query = $query->where('pump_current_stocks.org_id', $request->org_id);
        }
        if (!(empty($request->category_id))) {
            $query = $query->where('master_items.category_id', $request->category_id);
        }
        if (!(empty($request->office_id))) {
            $query = $query->where('pump_current_stocks.office_id', $request->office_id);
        }
        if ($request->division_id) {
            $query = $query->where('pump_current_stocks.division_id', $request->division_id);
        }

        if ($request->district_id) {
            $query = $query->where('pump_current_stocks.district_id', $request->district_id);
        }

        if ($request->upazilla_id) {
            $query = $query->where('pump_current_stocks.upazilla_id', $request->upazilla_id);
        }

        if ($request->union_id) {
            $query = $query->where('pump_current_stocks.union_id', $request->union_id);
        }
        $datas = $query->get();
        return response([
            'success' => true,
            'message' => 'Report Data',
            'data' => $datas
        ]);
    }
    
}
