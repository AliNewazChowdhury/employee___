<?php

namespace App\Http\Controllers\PumpInfoManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\PumpInfoManagement\PumpOperatorValidation;
use App\Models\FarmerProfile\FarmerBasicInfos;
use App\Models\PumpInfoManagement\PumpOperator;
use App\Models\PumpInfoManagement\PumpInfo;
use DB;
use PhpParser\Node\Stmt\Return_;

class PumpOperatorController extends Controller
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

    public function byPump_id(Request $request)
    {
        $query = DB::table('pump_operators')
                        ->join('pump_informations','pump_operators.pump_id', '=','pump_informations.id')
                        ->select('pump_operators.pump_id as id','pump_informations.pump_id')
                        ->distinct('pump_operators.pump_id');
        if ($request->org_id) {
            $query->where('pump_informations.org_id', $request->org_id);
        }
        $query->get();


        return response([
            'success' => true,
            'message' => 'Pump Scheduler list',
            'data' => $query
        ]);
    }

    /**
     * get all  pump Operator
     */
    public function index(Request $request)
    {
        $query = DB::table('pump_operators')
                        ->leftjoin('pump_informations','pump_operators.pump_id', '=','pump_informations.id')
                        ->select('pump_operators.*','pump_informations.pump_id as pump_info_pump_id', 'pump_operators.daily_task_entry_required');

        if ($request->org_id) {
            $query = $query->where('pump_operators.org_id', $request->org_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_operators.pump_id', $request->pump_id);
        }

        if ($request->name) {
            $query = $query->where('pump_operators.name', 'like', "{$request->name}%")
                            ->orWhere('pump_operators.name_bn', 'like', "{$request->name}%");
        }

        if ($request->mobile_no) {
            $query = $query->where('pump_operators.mobile_no', $request->mobile_no);
        }

        if ($request->nid) {
            $query = $query->where('pump_operators.nid', $request->nid);
        }

        if ($request->status) {
            $query = $query->where('pump_operators.status', $request->status);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Pump Operators list',
            'data'    => $list
        ]);
    }

    /**
     * pump Operator  store
     */
    public function store(Request $request)
    {   
        $validationResult = PumpOperatorValidation:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();

        try {
            $pumpOperator                       = new PumpOperator();
            $pumpOperator->org_id               = (int)$request->org_id;
            $pumpOperator->pump_id              = (int)$request->pump_id;
            $pumpOperator->name                 = $request->name;
            $pumpOperator->name_bn              = $request->name_bn;
            $pumpOperator->father_name          = $request->father_name;
            $pumpOperator->father_name_bn       = $request->father_name_bn;
            $pumpOperator->mother_name          = $request->mother_name;
            $pumpOperator->mother_name_bn       = $request->mother_name_bn;
            $pumpOperator->nid                  = (int)$request->nid;
            $pumpOperator->village_name         = $request->village_name;
            $pumpOperator->village_name_bn      = $request->village_name_bn;
            $pumpOperator->mobile_no            = $request->mobile_no;
            $pumpOperator->email                = $request->email;
            $pumpOperator->latitude             = $request->latitude;
            $pumpOperator->longitude            = $request->longitude;
            $pumpOperator->daily_task_entry_required = $request->daily_task_entry_required;
            $pumpOperator->pump_operator_user_id    = $request->pump_operator_user_id;
            $pumpOperator->pump_operator_username   = $request->pump_operator_username;
            $pumpOperator->pump_operator_email      = $request->pump_operator_email;
            $pumpOperator->husband_name             = $request->husband_name;
            $pumpOperator->gender                   = $request->gender;
            $pumpOperator->created_by           = (int)user_id();
            $pumpOperator->updated_by           = (int)user_id();
            $pumpOperator->save();

			$farBasicInfos                  = new FarmerBasicInfos();
            $farBasicInfos->farmer_id       = $request->pump_operator_user_id;
            $farBasicInfos->email           = $request->email?? username();
            $farBasicInfos->name            = $request->name;
            $farBasicInfos->mobile_no       = $request->mobile_no;
            $farBasicInfos->name_bn         = $request->name_bn;
            $farBasicInfos->gender          = $request->gender;
            $farBasicInfos->father_name     = $request->father_name;
            $farBasicInfos->father_name_bn  = $request->father_name_bn;
            $farBasicInfos->mother_name     = $request->mother_name;
            $farBasicInfos->mother_name_bn  = $request->mother_name_bn;
            $farBasicInfos->nid             = $request->nid;
            $farBasicInfos->far_division_id = (int)$request->division_id;
            $farBasicInfos->far_district_id = (int)$request->district_id;
            $farBasicInfos->far_upazilla_id = (int)$request->upazilla_id;
            $farBasicInfos->far_union_id    = (int)$request->union_id;
            $farBasicInfos->far_village     = $request->village_name;
            $farBasicInfos->far_village_bn  = $request->village_name_bn;
            $farBasicInfos->status          = 2;
            $farBasicInfos->save();

            save_log([
                'data_id' => $pumpOperator->id,
                'table_name' => 'pump_operators',
            ]);

            DB::commit();

        } catch (\Exception $ex) {

            DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $pumpOperator
        ]);
    }

    /**
     * pump Operator  Update
     */
    public function update(Request $request, $id)
    {
        $validationResult = PumpOperatorValidation:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $pumpOperator = PumpOperator::find($id);

        if (!$pumpOperator) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $pumpOperator->org_id               = (int)$request->org_id;
            $pumpOperator->pump_id              = (int)$request->pump_id;
            $pumpOperator->name                 = $request->name;
            $pumpOperator->name_bn              = $request->name_bn;
            $pumpOperator->father_name          = $request->father_name;
            $pumpOperator->father_name_bn       = $request->father_name_bn;
            $pumpOperator->mother_name          = $request->mother_name;
            $pumpOperator->mother_name_bn       = $request->mother_name_bn;
            $pumpOperator->nid                  = (int)$request->nid;
            $pumpOperator->village_name         = $request->village_name;
            $pumpOperator->village_name_bn      = $request->village_name_bn;
            $pumpOperator->mobile_no            = $request->mobile_no;
            $pumpOperator->email                = $request->email;
            $pumpOperator->latitude             = $request->latitude;
            $pumpOperator->longitude            = $request->longitude;
            $pumpOperator->daily_task_entry_required = $request->daily_task_entry_required;
            $pumpOperator->husband_name             = $request->husband_name;
            $pumpOperator->gender                   = $request->gender;
            $pumpOperator->updated_by            = (int)user_id();
            $pumpOperator->update();

            save_log([
                'data_id' => $pumpOperator->id,
                'table_name' => 'pump_operators',
                'execution_type' => 1
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $pumpOperator
        ]);
    }

    /**
     * Pump Operator Toggle Status
     */
    public function toggleStatus($id)
    {
        $pumpOperator = PumpOperator::find($id);

        if (!$pumpOperator) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $pumpOperator->status = $pumpOperator->status ? 0 : 1;
        $pumpOperator->update();

        save_log([
            'data_id' => $pumpOperator->id,
            'table_name' => 'pump_operators',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $pumpOperator
        ]);
    }

    /**
     * Pump Operator Delete
     */
    public function destroy($id)
    {
        $pumpOperator = PumpOperator::find($id);

        if (!$pumpOperator) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $pumpOperator->delete();

        save_log([
            'data_id' => $pumpOperator->id,
            'table_name' => 'pump_operators',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    /**
     * Pump Operator Report List
     */
    public function getreportlist(Request $request)
    {
        $query = DB::table('pump_operators')
                        ->leftjoin('pump_informations','pump_operators.pump_id', '=','pump_informations.id')
                        ->select('pump_informations.*',
                                    'pump_operators.village_name','pump_operators.village_name_bn',
                                    'pump_operators.mobile_no','pump_operators.name','pump_operators.name_bn')
                        ->where('pump_informations.status', 0);

        if ($request->village_name) {
            $query = $query->where('pump_operators.village_name', 'like', "{$request->village_name}%")
                            ->orWhere('pump_operators.village_name_bn', 'like', "{$request->village_name}%");
        }

        if ($request->name) {
            $query = $query->where('pump_operators.name', 'like', "{$request->name}%")
                            ->orWhere('pump_operators.name_bn', 'like', "{$request->name}%");
        }

        if ($request->org_id) {
            $query = $query->where('pump_operators.org_id', $request->org_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_operators.pump_id', $request->pump_id);
        }

        if ($request->division_id) {
            $query = $query->where('pump_informations.division_id', $request->division_id);
        }

        if ($request->district_id) {
            $query = $query->where('pump_informations.district_id', $request->district_id);
        }

        if ($request->upazilla_id) {
            $query = $query->where('pump_informations.upazilla_id', $request->upazilla_id);
        }

        if ($request->union_id) {
            $query = $query->where('pump_informations.union_id', $request->union_id);
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Pump Operators Report',
            'data'    => $list
        ]);
    }
}
