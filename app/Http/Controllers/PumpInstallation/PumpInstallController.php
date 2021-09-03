<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Models\Config\MasterContractor;
use App\Models\Config\MasterPumpInstallationProgressType;
use App\Models\Config\PumpProgressTypeStep;
use App\Models\FarmerOperator\FarmerComplain;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\FarmerOperator\FarmerSchemeApplicationDetails;
use App\Models\PumpInfoManagement\PumpInfo;
use App\Models\PumpInstallation\PumpInstall;
use App\Models\PumpMaintenance\Resunk;
use Illuminate\Http\Request;
use DB;

class PumpInstallController extends Controller
{
    /**
     * get contractors
     */
    public function contractors () 
    {
        $query = MasterContractor::select('id','contractor_name','contractor_name_bn')
                        ->where('status', 0)
                        ->get()
                        ->toArray();
        return response([
            'success' => true,
            'message' => 'Contractors list',
            'data'    => $query
        ]);
    }

    /**
     * get progress steps
     */
    public function pumpProgressTypes ($pump_type_id, $application_type_id, $scheme_application_id) 
    { 
        $pumpProgressType = MasterPumpInstallationProgressType::where('pump_type_id', $pump_type_id)
                            ->where('application_type_id', $application_type_id)
                            ->first();

        $progres_types = PumpProgressTypeStep::where('pump_progress_type_id', $pumpProgressType->id)
                ->with(['farPumpInstall' => function ($q) use ($scheme_application_id) {
                    $q->where('scheme_application_id', $scheme_application_id);
                }])
                ->where('status', 0)
                ->get()->toArray();
        
        if (!empty($progres_types)) {
            foreach($progres_types as $key=>$progres_type) {
                $progres_types[$key]['is_there'] =false;
                if(!empty($progres_type['far_pump_install'])){
                    $progres_types[$key]['is_there'] =true;
                }
            }
        }

        return response([
            'success' => true,
            'message' => 'Progress type list',
            'data'    => $progres_types
        ]);
    }

    /**
     * get pump install progress update
     */
    public function progressUpdate (Request $request) 
    {  
        DB::beginTransaction();

        try {
            $schemeApplicationId    = $request[0]['scheme_application_id'];

            PumpInstall::where('scheme_application_id', $schemeApplicationId)->delete();

            $totalProgressStep = count($request->all());
            foreach($request->all() as  $tmp) {

                if ($tmp['far_pump_install'][0]['start_date'] != null && $tmp['far_pump_install'][0]['end_date'] != null && $tmp['far_pump_install'][0]['note'] != null && $tmp['far_pump_install'][0]['note_bn'] != null)  {
                    $fpi                        = new PumpInstall();
                    $fpi->scheme_application_id = $tmp['scheme_application_id'];
                    $fpi->contractor_id         = $tmp['contractor_id'];
                    $fpi->pump_progress_type_step_id= $tmp['id'];
                    $fpi->start_date            = (new \DateTime($tmp['far_pump_install'][0]['start_date']))->format('Y-m-d');
                    $fpi->end_date              = (new \DateTime($tmp['far_pump_install'][0]['end_date']))->format('Y-m-d');
                    $fpi->note                  = $tmp['far_pump_install'][0]['note'];
                    $fpi->note_bn               = $tmp['far_pump_install'][0]['note_bn'];
                    $fpi->save();
                }
            }

            $schemeApp = FarmerSchemeApplication::find($schemeApplicationId);
            $schemeApp->status = 11; // 11 mean installation start
            $schemeApp->update();

            $totalPumpInstallCompleteStep = PumpInstall::where('scheme_application_id', $schemeApplicationId)->count('id');

            if ($totalProgressStep == $totalPumpInstallCompleteStep) {

                $schemeApplication  = FarmerSchemeApplicationDetails::join('far_scheme_application','far_scheme_application.id','far_scheme_app_details.scheme_application_id')
                                        ->select('far_scheme_application.application_type_id','far_scheme_application.pump_id',
                                            'far_scheme_application.org_id','far_scheme_app_details.pump_division_id',
                                            'far_scheme_app_details.pump_district_id','far_scheme_app_details.pump_upazilla_id',
                                            'far_scheme_app_details.pump_union_id','far_scheme_app_details.pump_mauza_no',
                                            'far_scheme_app_details.pump_jl_no','far_scheme_app_details.pump_plot_no',
                                            'far_scheme_application.id'
                                        )
                                        ->where('far_scheme_application.id', $schemeApplicationId)
                                        ->first();

                // when resunk pump install
                if ($schemeApplication->application_type_id == 2) {
                    
                    $getResunk = DB::table('far_resunks')
                                ->leftjoin('pump_informations','far_resunks.pump_informations_id','pump_informations.id')
                                ->select('far_resunks.id','far_resunks.complain_id')
                                ->where('pump_informations.id', $schemeApplication->pump_id)
                                ->orderBy('far_resunks.id','DESC')
                                ->first();
                    
                    $resunk = Resunk::find($getResunk->id);
                    $resunk->status = 2; // 2 mean resunk, pump install complete
                    $resunk->update();

                    $resunk = FarmerComplain::find($getResunk->complain_id);
                    $resunk->status = 7; // 7 mean complain complete
                    $resunk->update();
                }

                // when new pump install
                if ($schemeApplication->application_type_id == 1) { 
                    $pumpInfo               = new PumpInfo();
                    $pumpInfo->org_id       = $schemeApplication->org_id;
                    $pumpInfo->pump_id      = $this->getAutoGeneratedPumpId();
                    $pumpInfo->project_id   = 1;
                    $pumpInfo->division_id  = $schemeApplication->pump_division_id;
                    $pumpInfo->district_id  = $schemeApplication->pump_district_id;
                    $pumpInfo->upazilla_id  = $schemeApplication->pump_upazilla_id;
                    $pumpInfo->union_id     = $schemeApplication->pump_union_id;
                    $pumpInfo->mouza_no     = $schemeApplication->pump_mauza_no;
                    $pumpInfo->jl_no        = $schemeApplication->pump_jl_no;
                    $pumpInfo->plot_no      = $schemeApplication->pump_plot_no;
                    $pumpInfo->water_group_id= 1;
                    $pumpInfo->latitude     = 0.0000;
                    $pumpInfo->longitude    = 0.0000;
                    $pumpInfo->type_id      = 1;
                    $pumpInfo->created_by   = (int)user_id();
                    $pumpInfo->updated_by   = (int)user_id(); 
                    $pumpInfo->save();
                }

                $schemeApp = FarmerSchemeApplication::find($schemeApplication->id);
                $schemeApp->status = 14; // 14 mean install complete
                $schemeApp->pump_id = isset($pumpInfo) ? $pumpInfo->id : Null;
                $schemeApp->update();
            }

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
            'message' => 'Data save successfully'
        ]);
        
    }

    /**
     * get auto generated pump id
     */
    public function getAutoGeneratedPumpId() {
        $pumpInfo = PumpInfo::select('pump_id')->orderBy('id','desc')->first();
        if ($pumpInfo != null) {
            $pump_id = $pumpInfo['pump_id'] + 1;
        } else {
            $pump_id = 100001;
        }
        return $pump_id;
    }

    /**
     * get irrigation farmer list
     */
    public function irrigationFarmerList(Request $request)
    {
        $query = DB::table('far_basic_infos')->select('*');

        if ($request->name) {
            $query = $query->where('name', 'like', "{$request->name}%")
                        ->orWhere('name_bn', 'like', "{$request->name}%");
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

        if ($request->nid) {
            $query = $query->where('nid', $request->nid);
        }

        if ($request->mobile_no) {
            $query = $query->where('mobile_no', $request->nimobile_nod);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Irrigation farmer list',
            'data' => $list
        ]);
    }
}
