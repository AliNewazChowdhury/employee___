<?php

namespace App\Http\Controllers\PumpOperator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\PumpOperator\PumpOperatorSuspensionValidations;
use App\Models\PumpOperator\PumpOperatorSuspension;
use DB;

class OperatorSuspensionController extends Controller
{
    public function index (Request $request)
    {
        $query = DB::table('pump_opt_suspensions')
                ->join('pump_operators', 'pump_operators.id', '=', 'pump_opt_suspensions.operator_id')
                ->join('pump_informations', 'pump_informations.id', '=', 'pump_opt_suspensions.pump_id')
                ->select(
                    'pump_operators.*',
                    'pump_opt_suspensions.operator_id',
                    'pump_opt_suspensions.message',
                    'pump_opt_suspensions.message_bn',
                    'pump_opt_suspensions.reason',
                    'pump_opt_suspensions.reason_bn',
                    'pump_informations.pump_id as pumpid',
                    'pump_informations.latitude as info_latitude',
                    'pump_informations.longitude as info_longitude'
                );

        if ($request->org_id) {
            $query = $query->where('pump_opt_suspensions.org_id', $request->org_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_opt_suspensions.pump_id', $request->pump_id);
        }

        if ($request->operator_id) {
            $query = $query->where('pump_opt_suspensions.operator_id', 'like', "%{$request->operator_id}%");
        }

        $list = $query->orderBy('pump_opt_suspensions.created_at', 'desc')
            ->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Pump Operator Suspension List',
            'data' => $list
        ]);
    }

    /**
     * Pump Operator Suspension store
     */
    public function store(Request $request)
    {
        $validationResult = PumpOperatorSuspensionValidations::validate($request);
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {
            $fpopapp                       = new PumpOperatorSuspension();
            $fpopapp->org_id               = (int)$request->org_id;
            $fpopapp->pump_id              = (int)$request->pump_id;
            $fpopapp->operator_id          = (int)$request->operator_id;
            $fpopapp->reason               = $request->reason;
            $fpopapp->reason_bn            = $request->reason_bn;
            $fpopapp->message              = $request->message;
            $fpopapp->message_bn           = $request->message_bn;
            $fpopapp->suspend_date         = $request->suspend_date;
            $fpopapp->save();

            DB::table('pump_operators')
                ->where('id', $request->operator_id)
                ->update(['status' => 1]);

            save_log([
                'data_id' => $fpopapp->id,
                'table_name' => 'pump_opt_suspensions',
            ]);
        } catch (\Exception $ex) {

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $fpopapp
        ]);
    }

    public function pumpList (Request $request)
    {
        $data = DB::table('pump_informations')
                ->where('org_id', $request->org_id)
                ->where('division_id', $request->division_id)
                ->where('district_id', $request->district_id)
                ->where('upazilla_id', $request->upazilla_id)
                ->where('union_id', $request->union_id)
                ->where('status', 0) // 0 = Active
                ->get(['id', 'pump_id']);

        return response([
            'success' => true,
            'message' => 'Pump list',
            'data' => $data
        ]);
    }

    public function optList (Request $request) {
        $data = DB::table('pump_operators')
                ->where('pump_id', $request->pump_id)
                ->where('status', 0)
                ->get(['id', 'pump_id', 'name', 'name_bn']);

        return response([
            'success' => true,
            'message' => 'Pump Operators list',
            'data' => $data
        ]);
    }
}
