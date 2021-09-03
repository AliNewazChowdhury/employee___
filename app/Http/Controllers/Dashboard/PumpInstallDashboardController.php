<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class PumpInstallDashboardController extends Controller
{
    public function overview(Request $request)
    {   
        $statusList = ['rejected' => 5, 'installed' => 14];
        $column = $request->org_id ? 'org_id' : ($request->far_upazilla_id ? 'far_upazilla_id' : null);
        $farSchemeApp = 'far_scheme_application.' . $column;

        try {

            $data = [
                'totalScheme'            => DB::table('far_scheme_application')
                                            ->where($column, $request->$column)
                                            ->count(),

                'schemeByStatus'        => $this->getCountByStatus('far_scheme_application', $statusList, $column),

                'totalLicensedScheme'   => DB::table('far_scheme_license')
                                            ->join('far_scheme_application',
                                            'far_scheme_license.scheme_application_id', '=', 'far_scheme_application.id')
                                            ->where($farSchemeApp, $request->$column)
                                            ->count(),

                'operatorTotal'         => DB::table('far_pump_opt_apps')
                                            ->where($column, $request->$column)
                                            ->count(),

                'approvedOperator'      => DB::table('far_pump_opt_apps')
                                            ->where('status', 3)
                                            ->where($column, $request->$column)
                                            ->count()
            ];

            return response([
                'success' => true,
                'message' => 'Scheme Status',
                'data' => $data
            ]);

        } catch(\Exception $ex) {

            return response([
                'success' => false,
                'message' => 'Failed to fetch data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }
    }

    protected function getCountByStatus($table, $statusList, $column)
    {
        $formatted = [];
        $items = DB::table($table)
        ->where($column, request()->$column)
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
}
