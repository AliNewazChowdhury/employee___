<?php

namespace App\Http\Controllers\PumpInfoManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\PumpInfoManagement\PumpInfoValidation;
use App\Models\PumpInfoManagement\PumpInfo;
use App\Models\PumpInfoManagement\PumpOperator;
use DB;

class PumpInfoController extends Controller
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
     * get all  pump infotmation
     */
    public function dashboard(Request $request)
    {
        $query =PumpInfo::latest();
        $query1 =PumpOperator::latest();
        if (!empty($request->org_id)) {
            $query = $query->where('org_id', $request->org_id);
            $query1 = $query1->where('org_id', $request->org_id);
        }
        return response([
            'success' => true,
            'message' => 'Smart card application list',
            'data' => array(
                'total_pump' =>$query->get()->count(),
                'total_operator' =>$query1->get()->count()
            )
        ]);
    }
    public function listAll(Request $request)
    {
        $query = DB::table('pump_informations')
                        ->Leftjoin('master_projects','pump_informations.project_id', '=','master_projects.id')
                        ->select('pump_informations.*',
                                    'master_projects.project_name','master_projects.project_name_bn')
                                    ->where('pump_informations.status', 0)
                                    ->get();

        return response([
            'success' => true,
            'message' => 'Pump Info  list',
            'data' => $query
        ]);
    }
    public function listAllId(Request $request)
    {
        $query = PumpInfo::get();

        return response([
            'success' => true,
            'message' => 'Pump Info  Id',
            'data' => $query
        ]);
    }
    /**
     * get all  pump information
     */
    public function index(Request $request)
    {
        $query = DB::table('pump_informations')
                        ->Leftjoin('master_projects','pump_informations.project_id', '=','master_projects.id')
                        ->select('pump_informations.*',
                                    'master_projects.project_name','master_projects.project_name_bn');

        if ($request->org_id) {
            $query = $query->where('pump_informations.org_id', $request->org_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_informations.pump_id', $request->pump_id);
        }

        if ($request->project_id) {
            $query = $query->where('pump_informations.project_id', $request->project_id);
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

        if ($request->water_group_id) {
            $query = $query->where('pump_informations.water_group_id', $request->water_group_id);
        }

        if ($request->status) {
            $query = $query->where('pump_informations.status', $request->status);
        }

        $list = $query->paginate($request->per_page ?? env('PER_PAGE') );

        if ($request->has('paginate') && $request->paginate == 'false') {
          $list = $query->get();
        }

        return response([
            'success' => true,
            'message' => 'Pump Info  list',
            'data' => $list
        ]);
    }

    /**
     * get all pump information
     */
    public function getAllPumpInfo(Request $request)
    {  
        $query = DB::table('pump_informations')
                        ->Leftjoin('master_projects','pump_informations.project_id', '=','master_projects.id')
                        ->select('pump_informations.*',
                                    'master_projects.project_name','master_projects.project_name_bn');

        if ($request->org_id) {
            $query = $query->where('pump_informations.org_id', $request->org_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_informations.pump_id', $request->pump_id);
        }

        if ($request->project_id) {
            $query = $query->where('pump_informations.project_id', $request->project_id);
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

        if ($request->water_group_id) {
            $query = $query->where('pump_informations.water_group_id', $request->water_group_id);
        }

        if ($request->status) {
            $query = $query->where('pump_informations.status', $request->status);
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Pump Info list',
            'data'    => $list
        ]);
    }

    /**
     * pump information  store
     */
    public function store(Request $request)
    {
        $validationResult = PumpInfoValidation:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {
            $pumpInfo                       = new PumpInfo();
            $pumpInfo->org_id               = (int)$request->org_id;
            $pumpInfo->pump_id              = $request->pump_id;
            $pumpInfo->project_id           = (int)$request->project_id;
            $pumpInfo->division_id          = (int)$request->division_id;
            $pumpInfo->district_id          = (int)$request->district_id;
            $pumpInfo->upazilla_id          = (int)$request->upazilla_id;
            $pumpInfo->union_id             = (int)$request->union_id;
            $pumpInfo->mouza_no             = $request->mouza_no;
            $pumpInfo->jl_no                = $request->jl_no;
            $pumpInfo->total_farmer         = $request->total_farmer;
            $pumpInfo->plot_no              = $request->plot_no;
            $pumpInfo->water_group_id       = $request->water_group_id;
            $pumpInfo->latitude             = $request->latitude;
            $pumpInfo->longitude            = $request->longitude;
            $pumpInfo->created_by           = (int)user_id();
            $pumpInfo->updated_by           = (int)user_id();
            $pumpInfo->save();

            save_log([
                'data_id' => $pumpInfo->id,
                'table_name' => 'pump_informations',
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $pumpInfo
        ]);
    }

    /**
     * pump information  Update
     */
    public function update(Request $request, $id)
    {
        $validationResult = PumpInfoValidation:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $pumpInfo = PumpInfo::find($id);

        if (!$pumpInfo) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $pumpInfo->org_id               = (int)$request->org_id;
            $pumpInfo->pump_id              = $request->pump_id;
            $pumpInfo->project_id           = (int)$request->project_id;
            $pumpInfo->division_id          = (int)$request->division_id;
            $pumpInfo->district_id          = (int)$request->district_id;
            $pumpInfo->upazilla_id          = (int)$request->upazilla_id;
            $pumpInfo->union_id             = (int)$request->union_id;
            $pumpInfo->mouza_no             = $request->mouza_no;
            $pumpInfo->jl_no                = $request->jl_no;
            $pumpInfo->total_farmer         = $request->total_farmer;
            $pumpInfo->plot_no              = $request->plot_no;
            $pumpInfo->water_group_id       = $request->water_group_id;
            $pumpInfo->latitude             = $request->latitude;
            $pumpInfo->longitude            = $request->longitude;
            $pumpInfo->updated_by         = (int)user_id();
            $pumpInfo->update();

            save_log([
                'data_id' => $pumpInfo->id,
                'table_name' => 'pump_informations',
                'execution_type' => 1
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $pumpInfo
        ]);
    }

    /**
     * Pump Info Toggle Status
     */
    public function toggleStatus($id)
    {
        $pumpInfo = PumpInfo::find($id);

        if (!$pumpInfo) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $pumpInfo->status = $pumpInfo->status ? 0 : 1;
        $pumpInfo->update();

        save_log([
            'data_id' => $pumpInfo->id,
            'table_name' => 'pump_informations',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $pumpInfo
        ]);
    }

    /**
     * Pump Info Delete
     */
    public function destroy($id)
    {
        $notSetting = PumpInfo::find($id);

        if (!$notSetting) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $notSetting->delete();

        save_log([
            'data_id' => $notSetting->id,
            'table_name' => 'pump_informations',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    /**
     * Pump report list
     */

    public function reportlist(Request $request)
    {
        $query = PumpInfo::select("*");

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->project_id) {
            $query = $query->where('project_id', $request->project_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_id', $request->pump_id);
        }

        if ($request->division_id) {
            $query = $query->where('division_id', $request->division_id);
        }

        if ($request->district_id) {
            $query = $query->where('district_id', $request->district_id);
        }

        if ($request->upazilla_id) {
            $query = $query->where('upazilla_id', $request->upazilla_id);
        }

        if ($request->union_id) {
            $query = $query->where('union_id', $request->union_id);
        }

        if ($request->mouza_no) {
            $query = $query->where('mouza_no', $request->mouza_no);
        }

        if ($request->jl_no) {
            $query = $query->where('jl_no', $request->jl_no);
        }

        if ($request->plot) {
            $query = $query->where('plot', $request->plot);
        }

        $reportList= $query->get();

        if(!$reportList)  {
            return response([
                'success' => false,
                'message' => 'Data Not Found',
            ]);
        }else {

            return response([
                'success' => true,
                'message' => 'Pump Report  list',
                'data' => $reportList
            ]);

        }


    }

}
