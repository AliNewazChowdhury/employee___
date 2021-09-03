<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IrrigationDashboardController extends Controller
{
    // Returning Scheme Status Count
    public function scheme(Request $request)
    {
        $statusList = ['pending' => 1, 'approved' => 3, 'rejected' => 5];
        $column = $request->org_id ? 'org_id' : ($request->far_upazilla_id ? 'far_upazilla_id' : null);
        $farSchemeApp = 'far_scheme_application.' . $column;

        $data = $this->getCountByStatus('far_scheme_application', $statusList, $column);
        $schemeApplications = $this->getTotal('far_scheme_application', $column);
        $data['total'] = $schemeApplications;
        $data['pending'] = DB::table('far_scheme_application')
            ->where($column, $request->$column)
            ->whereIn('status', [1, 2, 4, 6, 7, 8, 9, 10, 11, 12, 13])
            ->count();

        return response([
            'success' => true,
            'message' => 'Scheme Status',
            'data' => $data
        ]);
    }

    // Returning Complain Status Count
    public function complain(Request $request)
    {
        $statusList = ['pending' => 1, 'complete' => 7, 'reviewed' => 3, 'resolved' => 2];
        $column = $request->org_id ? 'org_id' : ($request->far_upazilla_id ? 'far_upazilla_id' : null);

        $data = $this->getCountByStatus('far_complains', $statusList, $column);
        $complains = $this->getTotal('far_complains', $column);
        $data['total'] = $complains;

        return response([
            'success' => true,
            'message' => 'Complain Status',
            'data' => $data
        ]);
    }

    // Returning Total Count
    public function totalApps(Request $request)
    {
        $column = $request->org_id ? 'org_id' : ($request->far_upazilla_id ? 'far_upazilla_id' : null);

        $pumpInstalled  = DB::table('far_scheme_application')
                            ->where('status', 11) // 11 = Installation
                            ->where($column, $request->$column)
                            ->count();

        $smartCards     = DB::table('far_smart_card_apps')
                            ->where($column, $request->$column)
                            ->count();

        $waterTests     = DB::table('far_water_test_apps')
                            ->where($column, $request->$column)
                            ->count();

        $data = compact('pumpInstalled', 'smartCards', 'waterTests');

        return response([
            'success' => true,
            'message' => 'Total Data',
            'data' => $data
        ]);
    }


    protected function getCountByStatus($table, $statusList, $column)
    {
        $formatted = [];
        $items = DB::table($table)
        ->where($column, \request()->$column)
        ->select(DB::raw('count(*) as application_count, status'))
                 ->groupBy('status')
                 ->get()
                 ->toArray();
        foreach($items as $item) {
            $key = array_search($item->status, $statusList, true);
            if ($key) {
                $formatted[$key] = $item->application_count;
            }
        }
        return $formatted;
    }

    protected function getTotal ($table, $column) {
        return DB::table($table)
            ->where($column, \request()->$column)
            ->count();
    }
}
