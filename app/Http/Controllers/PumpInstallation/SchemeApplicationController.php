<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\SchemeApplicationApproval;
use App\Models\Config\MasterPumpInstallationProgressType;
use App\Models\Config\PumpProgressTypeStep;
use App\Library\RestService;
use DB;

class SchemeApplicationController extends Controller
{
    /**
    * show all application which payment_status = 1
    */
    public function index(Request $request)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id')
                        ->leftjoin('far_scheme_projects','far_scheme_application.id', '=','far_scheme_projects.scheme_application_id')
                        ->leftjoin('far_scheme_surveys','far_scheme_application.id', '=','far_scheme_surveys.scheme_application_id')
                        ->leftjoin('pump_informations','far_scheme_application.pump_id', '=','pump_informations.id')
                        ->select('far_scheme_application.*',
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn',
                                'far_scheme_projects.project_id as scheme_project_id',
                                'far_scheme_surveys.id as survey_id',
                                'pump_informations.pump_id'
                            )
                        ->where('far_scheme_application.payment_status', 1)
                        ->where('far_scheme_application.status', '!=', 4)
                        ->orderBy('far_scheme_application.id','DESC');

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
             $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        }

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        if ($request->far_village) {
            $query = $query->where('far_scheme_application.far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->status) {
            $query = $query->where('far_scheme_application.status', $request->status);
        }

        if ($request->payment_status) {
            $query = $query->where('far_scheme_application.payment_status', $request->payment_status);
        }

        if ($request->sch_man_name) {
            $query = $query->where('far_scheme_app_details.sch_man_name', 'like', "{$request->sch_man_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_name_bn', 'like', "{$request->sch_man_name}%");
        }

        if ($request->sch_man_district_id) {
            $query = $query->where('far_scheme_app_details.sch_man_district_id', $request->sch_man_district_id);
        }

        if ($request->sch_man_division_id) {
            $query = $query->where('far_scheme_app_details.sch_man_division_id', $request->sch_man_division_id);
        }

        if ($request->sch_man_upazilla_id) {
            $query = $query->where('far_scheme_app_details.sch_man_upazilla_id', $request->sch_man_upazilla_id);
        }

        if ($request->sch_man_union_id) {
            $query = $query->where('far_scheme_app_details.sch_man_union_id', $request->sch_man_union_id);
        }

        if ($request->sch_man_village) {
             $query = $query->where('far_scheme_app_details.sch_man_village', 'like', "{$request->sch_man_village}%")
                            ->orWhere('far_scheme_app_details.sch_man_village_bn', 'like', "{$request->sch_man_village}%");
        }

         if ($request->scheme_application_id) {
            $query = $query->where('far_scheme_app_details.scheme_application_id', $request->scheme_application_id);
        }


        if ($request->sch_man_father_name) {
             $query = $query->where('far_scheme_app_details.sch_man_father_name', 'like', "{$request->sch_man_father_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_father_name_bn', 'like', "{$request->sch_man_father_name}%");
        }

        if ($request->sch_man_mother_name) {
             $query = $query->where('far_scheme_app_details.sch_man_mother_name', 'like', "{$request->sch_man_mother_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_mother_name_bn', 'like', "{$request->sch_man_mother_name}%");
        }

        if ($request->sch_man_mobile_no) {
            $query = $query->where('far_scheme_app_details.sch_man_mobile_no', $request->sch_man_mobile_no);
        }

        if ($request->pump_district_id) {
            $query = $query->where('far_scheme_app_details.pump_district_id', $request->pump_district_id);
        }

        if ($request->pump_upazilla_id) {
            $query = $query->where('far_scheme_app_details.pump_upazilla_id', $request->pump_upazilla_id);
        }

        if ($request->pump_union_id) {
            $query = $query->where('far_scheme_app_details.pump_union_id', $request->pump_union_id);
        }

        if ($request->pump_mauza_no) {
             $query = $query->where('far_scheme_app_details.pump_mauza_no', 'like', "{$request->pump_mauza_no}%")
                            ->orWhere('far_scheme_app_details.pump_mauza_no_bn', 'like', "{$request->pump_mauza_no}%");
        }

        if ($request->pump_jl_no) {
             $query = $query->where('far_scheme_app_details.pump_jl_no', 'like', "{$request->pump_jl_no}%")
                            ->orWhere('far_scheme_app_details.pump_jl_no_bn', 'like', "{$request->pump_jl_no}%");
        }

        if ($request->pump_plot_no) {
             $query = $query->where('far_scheme_app_details.pump_plot_no', 'like', "{$request->pump_plot_no}%")
                            ->orWhere('far_scheme_app_details.pump_plot_no_bn', 'like', "{$request->pump_plot_no}%");
        }

        if ($request->general_minutes) {
            $query = $query->where('pump_plot_no.general_minutes', $request->general_minutes);
        }

        if ($request->scheme_lands) {
            $query = $query->where('pump_plot_no.scheme_lands', $request->scheme_lands);
        }

          if ($request->scheme_map) {
            $query = $query->where('far_scheme_app_details.scheme_map', $request->scheme_map);
        }

        if ($request->affidavit_id) {
            $query = $query->where('far_scheme_app_details.affidavit_id', $request->affidavit_id);
        }


        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Application list',
            'data' => $list
        ]);
    }

    /**
    * show all application which payment_status = 15
    */
    public function receiveApplicationList(Request $request)
    {  
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id')
                        ->join('far_scheme_approvals','far_scheme_application.id', '=','far_scheme_approvals.scheme_application_id')
                        ->leftjoin('far_scheme_projects','far_scheme_application.id', '=','far_scheme_projects.scheme_application_id')
                        ->leftjoin('far_scheme_surveys','far_scheme_application.id', '=','far_scheme_surveys.scheme_application_id')
                        ->select('far_scheme_application.*',
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn',
                                'far_scheme_projects.project_id as scheme_project_id',
                                'far_scheme_surveys.id as survey_id'
                            )
                        ->where('far_scheme_application.status', 15) // 15 mean send
                        ->where('far_scheme_approvals.receiver_id', $request->receiver_id)
                        ->where('far_scheme_approvals.status', 1) //1 mean pending
                        ->orderBy('far_scheme_application.id','DESC');

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
             $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        }

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success'   => true,
            'message'   => 'Receiver Application list',
            'data'      => $list
        ]);
    }

    /**
    * show single application
    */
    public function show($id)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id')
                        ->leftjoin('far_scheme_projects','far_scheme_application.id', '=','far_scheme_projects.scheme_application_id')
                        ->leftjoin('master_projects','far_scheme_projects.project_id', '=','master_projects.id')
                        ->leftjoin('far_scheme_surveys','far_scheme_application.id', '=','far_scheme_surveys.scheme_application_id')
                        ->leftjoin('far_scheme_notes','far_scheme_application.id', '=','far_scheme_notes.scheme_application_id')
                        ->leftjoin('far_scheme_license','far_scheme_application.id', '=','far_scheme_license.scheme_application_id')
                        ->leftjoin('pump_informations','far_scheme_application.pump_id', '=','pump_informations.id')
                        ->leftjoin('far_scheme_requisitions','far_scheme_application.id', '=','far_scheme_requisitions.scheme_application_id')
                        ->select('far_scheme_application.*',
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn',
                                'master_projects.project_name',
                                'master_projects.project_name_bn',
                                'far_scheme_projects.id as project_id',
                                'pump_informations.pump_id',
                                'far_scheme_requisitions.pump_type_id',
                                'far_scheme_surveys.suggestion','far_scheme_surveys.suggestion_bn','far_scheme_surveys.id as survey_id','far_scheme_surveys.survey_date',
                                'far_scheme_license.id as license_id','far_scheme_license.license_no','far_scheme_license.issue_date as license_issue_date','far_scheme_license.attachment as license_attachment'
                        )
                        ->where('far_scheme_application.id', $id)
                        ->first();

        $survey_notes = DB::table('far_scheme_application')
                            ->leftjoin('far_scheme_notes','far_scheme_application.id', '=','far_scheme_notes.scheme_application_id')
                            ->select('far_scheme_notes.note','far_scheme_notes.note_bn')
                            ->where('scheme_application_id', $id)
                            ->get();

        $pumpProgressType = MasterPumpInstallationProgressType::where('pump_type_id', $query->pump_type_id)
                                ->where('application_type_id', $query->application_type_id)
                                ->first();
        if ($pumpProgressType) {
            $progress_types = PumpProgressTypeStep::where('pump_progress_type_id', $pumpProgressType->id)
                ->with(['farPumpInstall' => function ($q) use ($id) {
                    $q->where('scheme_application_id', $id);
                }])
                ->where('status', 0)
                ->get()->toArray();

            if (!empty($progress_types)) {
                foreach($progress_types as $key=>$progres_type) {
                    $progress_types[$key]['is_there'] =false;
                    if(!empty($progres_type['far_pump_install'])){
                        $progress_types[$key]['is_there'] =true;
                    }
                }
            }
        }
        

        return response([
            'success' => true,
            'message' => 'Application',
            'data'    => $query,
            'survey_notes'    => $survey_notes,
            'progress_types'  => isset($progress_types) ? $progress_types : []
        ]);
    }

    /**
    * show scheme application log
    */
    public function log($id)
    {
        $records = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->join('far_scheme_approvals','far_scheme_application.id', '=','far_scheme_approvals.scheme_application_id')
                        ->select('far_scheme_application.application_id',
                            'far_scheme_approvals.sender_id','far_scheme_approvals.receiver_id',
                            'far_scheme_approvals.note','far_scheme_approvals.note_bn','far_scheme_approvals.status'
                        )
                        ->where('far_scheme_approvals.scheme_application_id', $id)
                        ->get()
                        ->toArray();

        $sender_ids = array_column($records, 'sender_id');
        $receiver_ids = array_column($records, 'receiver_id');
        $user_ids = $sender_ids + $receiver_ids;
        $user_ids = array_unique($user_ids);

        $baseUrl = config('app.base_url.auth_service');
        $uri = '/user/user-list';

        $users = RestService::getData($baseUrl, $uri, ['user_ids' => $user_ids]);
        $users = json_decode($users, true);

        return response([
            'success' => true,
            'message' => 'Application Log',
            'data'    => $records,
            'users'   => $users
        ]);
    }

    /**
     * scheme application status update
     */
    public function send(Request $request)
    {  
        $scheme_application = FarmerSchemeApplication::find($request->id);

        if (!$scheme_application) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {

            $exist_sapp = SchemeApplicationApproval::where('scheme_application_id',$request->id)->first();

            if ($exist_sapp != null) {
                $exist_sapp->status = 2; // 2 mean progress
                $exist_sapp->update();
            } 
            
            $sapp                           = new SchemeApplicationApproval();
            $sapp->scheme_application_id    = $request->id;
            $sapp->sender_id                = $request->sender_id;
            $sapp->receiver_id              = $request->receiver_id;
            $sapp->note                     = $request->note;
            $sapp->note_bn                  = $request->note_bn; 
            $sapp->created_by               =(int)user_id();
            $sapp->updated_by               =(int)user_id();
            $sapp->save();

            save_log([
                'data_id'       => $sapp->id,
                'table_name'    => 'far_scheme_approvals'
            ]);            

            $scheme_application->status = 15; // 15 mean send
            $scheme_application->update();

            save_log([
                'data_id'        => $scheme_application->id,
                'table_name'     => 'far_scheme_application',
                'execution_type' => 2
            ]);

            DB::commit();

        } catch (\Exception $ex) {

            DB::rollback();
            
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $scheme_application
        ]);
    }

    /**
     * scheme application status update
     */
    public function statusUpdate($id, $status)
    {
        $scheme_application = FarmerSchemeApplication::find($id);

        if (!$scheme_application) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }
        DB::beginTransaction();

        try {

            $exist_sapp = SchemeApplicationApproval::where('scheme_application_id',$id)->where('status', 1)->first();

            if ($exist_sapp != null) {
                $exist_sapp->status = 3; // 3 mean approve
                $exist_sapp->update();
            } 


            $scheme_application->status = $status;
            $scheme_application->update();

            save_log([
                'data_id'        => $scheme_application->id,
                'table_name'     => 'far_scheme_application',
                'execution_type' => 2
            ]);

            DB::commit();

        } catch (\Exception $ex) {

            DB::rollback();
            
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $scheme_application
        ]);
    }

    /**
     * scheme application license status update to 6
     */
    public function licenseStatusUpdate($id, $isLicense)
    {
        $scheme_application = FarmerSchemeApplication::find($id);

        if (!$scheme_application) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $scheme_application->status     = 6;
        $scheme_application->is_license = $isLicense;
        $scheme_application->update();

        save_log([
            'data_id'        => $scheme_application->id,
            'table_name'     => 'far_scheme_application',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $scheme_application
        ]);
    }

    /**
    * show all application which status = 3 mean select
    */
    public function licenseApplicationList(Request $request)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id')
                        ->select('far_scheme_application.*',
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn'
                            )
                        ->where('far_scheme_application.status', 3);

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
                $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        }

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        if ($request->far_village) {
            $query = $query->where('far_scheme_application.far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->status) {
            $query = $query->where('far_scheme_application.status', $request->status);
        }

        if ($request->payment_status) {
            $query = $query->where('far_scheme_application.payment_status', $request->payment_status);
        }

        if ($request->sch_man_name) {
            $query = $query->where('far_scheme_app_details.sch_man_name', 'like', "{$request->sch_man_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_name_bn', 'like', "{$request->sch_man_name}%");
        }

        if ($request->sch_man_district_id) {
            $query = $query->where('far_scheme_app_details.sch_man_district_id', $request->sch_man_district_id);
        }

        if ($request->sch_man_division_id) {
            $query = $query->where('far_scheme_app_details.sch_man_division_id', $request->sch_man_division_id);
        }

        if ($request->sch_man_upazilla_id) {
            $query = $query->where('far_scheme_app_details.sch_man_upazilla_id', $request->sch_man_upazilla_id);
        }

        if ($request->sch_man_union_id) {
            $query = $query->where('far_scheme_app_details.sch_man_union_id', $request->sch_man_union_id);
        }

        if ($request->sch_man_village) {
                $query = $query->where('far_scheme_app_details.sch_man_village', 'like', "{$request->sch_man_village}%")
                            ->orWhere('far_scheme_app_details.sch_man_village_bn', 'like', "{$request->sch_man_village}%");
        }

            if ($request->scheme_application_id) {
            $query = $query->where('far_scheme_app_details.scheme_application_id', $request->scheme_application_id);
        }


        if ($request->sch_man_father_name) {
                $query = $query->where('far_scheme_app_details.sch_man_father_name', 'like', "{$request->sch_man_father_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_father_name_bn', 'like', "{$request->sch_man_father_name}%");
        }

        if ($request->sch_man_mother_name) {
                $query = $query->where('far_scheme_app_details.sch_man_mother_name', 'like', "{$request->sch_man_mother_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_mother_name_bn', 'like', "{$request->sch_man_mother_name}%");
        }

        if ($request->sch_man_mobile_no) {
            $query = $query->where('far_scheme_app_details.sch_man_mobile_no', $request->sch_man_mobile_no);
        }

        if ($request->pump_district_id) {
            $query = $query->where('far_scheme_app_details.pump_district_id', $request->pump_district_id);
        }

        if ($request->pump_upazilla_id) {
            $query = $query->where('far_scheme_app_details.pump_upazilla_id', $request->pump_upazilla_id);
        }

        if ($request->pump_union_id) {
            $query = $query->where('far_scheme_app_details.pump_union_id', $request->pump_union_id);
        }

        if ($request->pump_mauza_no) {
                $query = $query->where('far_scheme_app_details.pump_mauza_no', 'like', "{$request->pump_mauza_no}%")
                            ->orWhere('far_scheme_app_details.pump_mauza_no_bn', 'like', "{$request->pump_mauza_no}%");
        }

        if ($request->pump_jl_no) {
                $query = $query->where('far_scheme_app_details.pump_jl_no', 'like', "{$request->pump_jl_no}%")
                            ->orWhere('far_scheme_app_details.pump_jl_no_bn', 'like', "{$request->pump_jl_no}%");
        }

        if ($request->pump_plot_no) {
                $query = $query->where('far_scheme_app_details.pump_plot_no', 'like', "{$request->pump_plot_no}%")
                            ->orWhere('far_scheme_app_details.pump_plot_no_bn', 'like', "{$request->pump_plot_no}%");
        }

        if ($request->general_minutes) {
            $query = $query->where('pump_plot_no.general_minutes', $request->general_minutes);
        }

        if ($request->scheme_lands) {
            $query = $query->where('pump_plot_no.scheme_lands', $request->scheme_lands);
        }

            if ($request->scheme_map) {
            $query = $query->where('far_scheme_app_details.scheme_map', $request->scheme_map);
        }

        if ($request->affidavit_id) {
            $query = $query->where('far_scheme_app_details.affidavit_id', $request->affidavit_id);
        }


        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'License Application list',
            'data' => $list
        ]);
    }

    /**
    * show all current status application which status = 6
    */
    public function currentStatusApplicationList(Request $request)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->leftjoin('far_scheme_license','far_scheme_application.id', '=','far_scheme_license.scheme_application_id')
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id')
                        ->select('far_scheme_application.*',
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn',
                                'far_scheme_license.id as scheme_license_id',
                                'far_scheme_license.is_verified',
                                'far_scheme_license.attachment as license_attachment'
                            )
                        ->where('far_scheme_application.status', 6);

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
                $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        }

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        if ($request->far_village) {
            $query = $query->where('far_scheme_application.far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->status) {
            $query = $query->where('far_scheme_application.status', $request->status);
        }

        if ($request->payment_status) {
            $query = $query->where('far_scheme_application.payment_status', $request->payment_status);
        }

        if ($request->sch_man_name) {
            $query = $query->where('far_scheme_app_details.sch_man_name', 'like', "{$request->sch_man_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_name_bn', 'like', "{$request->sch_man_name}%");
        }

        if ($request->sch_man_district_id) {
            $query = $query->where('far_scheme_app_details.sch_man_district_id', $request->sch_man_district_id);
        }

        if ($request->sch_man_division_id) {
            $query = $query->where('far_scheme_app_details.sch_man_division_id', $request->sch_man_division_id);
        }

        if ($request->sch_man_upazilla_id) {
            $query = $query->where('far_scheme_app_details.sch_man_upazilla_id', $request->sch_man_upazilla_id);
        }

        if ($request->sch_man_union_id) {
            $query = $query->where('far_scheme_app_details.sch_man_union_id', $request->sch_man_union_id);
        }

        if ($request->sch_man_village) {
                $query = $query->where('far_scheme_app_details.sch_man_village', 'like', "{$request->sch_man_village}%")
                            ->orWhere('far_scheme_app_details.sch_man_village_bn', 'like', "{$request->sch_man_village}%");
        }

        if ($request->scheme_application_id) {
            $query = $query->where('far_scheme_app_details.scheme_application_id', $request->scheme_application_id);
        }

        if ($request->sch_man_father_name) {
                $query = $query->where('far_scheme_app_details.sch_man_father_name', 'like', "{$request->sch_man_father_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_father_name_bn', 'like', "{$request->sch_man_father_name}%");
        }

        if ($request->sch_man_mother_name) {
                $query = $query->where('far_scheme_app_details.sch_man_mother_name', 'like', "{$request->sch_man_mother_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_mother_name_bn', 'like', "{$request->sch_man_mother_name}%");
        }

        if ($request->sch_man_mobile_no) {
            $query = $query->where('far_scheme_app_details.sch_man_mobile_no', $request->sch_man_mobile_no);
        }

        if ($request->pump_district_id) {
            $query = $query->where('far_scheme_app_details.pump_district_id', $request->pump_district_id);
        }

        if ($request->pump_upazilla_id) {
            $query = $query->where('far_scheme_app_details.pump_upazilla_id', $request->pump_upazilla_id);
        }

        if ($request->pump_union_id) {
            $query = $query->where('far_scheme_app_details.pump_union_id', $request->pump_union_id);
        }

        if ($request->pump_mauza_no) {
                $query = $query->where('far_scheme_app_details.pump_mauza_no', 'like', "{$request->pump_mauza_no}%")
                            ->orWhere('far_scheme_app_details.pump_mauza_no_bn', 'like', "{$request->pump_mauza_no}%");
        }

        if ($request->pump_jl_no) {
                $query = $query->where('far_scheme_app_details.pump_jl_no', 'like', "{$request->pump_jl_no}%")
                            ->orWhere('far_scheme_app_details.pump_jl_no_bn', 'like', "{$request->pump_jl_no}%");
        }

        if ($request->pump_plot_no) {
                $query = $query->where('far_scheme_app_details.pump_plot_no', 'like', "{$request->pump_plot_no}%")
                            ->orWhere('far_scheme_app_details.pump_plot_no_bn', 'like', "{$request->pump_plot_no}%");
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'License Application list',
            'data' => $list
        ]);
    }

    /**
    * show all contract agreement application which status = 7
    */
    public function contractAgreementApplicationList(Request $request)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->leftjoin('far_scheme_license','far_scheme_application.id', '=','far_scheme_license.scheme_application_id')
                        ->leftjoin('far_scheme_agreement','far_scheme_application.id', '=','far_scheme_agreement.scheme_application_id')
                        ->leftjoin('far_scheme_agreemt_doc','far_scheme_application.id', '=','far_scheme_agreemt_doc.scheme_application_id')
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id')
                        ->select('far_scheme_application.*',
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn',
                                'far_scheme_license.license_no',
                                'far_scheme_agreement.id as agreement_id',
                                'far_scheme_agreemt_doc.id as agreement_doc_id',
                                'far_scheme_agreement.agreement_details',
                                'far_scheme_agreement.agreement_details_bn'
                            )
                        ->where('far_scheme_application.status', 7);

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
                $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        }

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        if ($request->far_village) {
            $query = $query->where('far_scheme_application.far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->status) {
            $query = $query->where('far_scheme_application.status', $request->status);
        }

        if ($request->payment_status) {
            $query = $query->where('far_scheme_application.payment_status', $request->payment_status);
        }

        if ($request->sch_man_name) {
            $query = $query->where('far_scheme_app_details.sch_man_name', 'like', "{$request->sch_man_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_name_bn', 'like', "{$request->sch_man_name}%");
        }

        if ($request->sch_man_district_id) {
            $query = $query->where('far_scheme_app_details.sch_man_district_id', $request->sch_man_district_id);
        }

        if ($request->sch_man_division_id) {
            $query = $query->where('far_scheme_app_details.sch_man_division_id', $request->sch_man_division_id);
        }

        if ($request->sch_man_upazilla_id) {
            $query = $query->where('far_scheme_app_details.sch_man_upazilla_id', $request->sch_man_upazilla_id);
        }

        if ($request->sch_man_union_id) {
            $query = $query->where('far_scheme_app_details.sch_man_union_id', $request->sch_man_union_id);
        }

        if ($request->sch_man_village) {
                $query = $query->where('far_scheme_app_details.sch_man_village', 'like', "{$request->sch_man_village}%")
                            ->orWhere('far_scheme_app_details.sch_man_village_bn', 'like', "{$request->sch_man_village}%");
        }

            if ($request->scheme_application_id) {
            $query = $query->where('far_scheme_app_details.scheme_application_id', $request->scheme_application_id);
        }


        if ($request->sch_man_father_name) {
                $query = $query->where('far_scheme_app_details.sch_man_father_name', 'like', "{$request->sch_man_father_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_father_name_bn', 'like', "{$request->sch_man_father_name}%");
        }

        if ($request->sch_man_mother_name) {
                $query = $query->where('far_scheme_app_details.sch_man_mother_name', 'like', "{$request->sch_man_mother_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_mother_name_bn', 'like', "{$request->sch_man_mother_name}%");
        }

        if ($request->sch_man_mobile_no) {
            $query = $query->where('far_scheme_app_details.sch_man_mobile_no', $request->sch_man_mobile_no);
        }

        if ($request->pump_district_id) {
            $query = $query->where('far_scheme_app_details.pump_district_id', $request->pump_district_id);
        }

        if ($request->pump_upazilla_id) {
            $query = $query->where('far_scheme_app_details.pump_upazilla_id', $request->pump_upazilla_id);
        }

        if ($request->pump_union_id) {
            $query = $query->where('far_scheme_app_details.pump_union_id', $request->pump_union_id);
        }

        if ($request->pump_mauza_no) {
                $query = $query->where('far_scheme_app_details.pump_mauza_no', 'like', "{$request->pump_mauza_no}%")
                            ->orWhere('far_scheme_app_details.pump_mauza_no_bn', 'like', "{$request->pump_mauza_no}%");
        }

        if ($request->pump_jl_no) {
                $query = $query->where('far_scheme_app_details.pump_jl_no', 'like', "{$request->pump_jl_no}%")
                            ->orWhere('far_scheme_app_details.pump_jl_no_bn', 'like', "{$request->pump_jl_no}%");
        }

        if ($request->pump_plot_no) {
                $query = $query->where('far_scheme_app_details.pump_plot_no', 'like', "{$request->pump_plot_no}%")
                            ->orWhere('far_scheme_app_details.pump_plot_no_bn', 'like', "{$request->pump_plot_no}%");
        }

        if ($request->general_minutes) {
            $query = $query->where('pump_plot_no.general_minutes', $request->general_minutes);
        }

        if ($request->scheme_lands) {
            $query = $query->where('pump_plot_no.scheme_lands', $request->scheme_lands);
        }

            if ($request->scheme_map) {
            $query = $query->where('far_scheme_app_details.scheme_map', $request->scheme_map);
        }

        if ($request->affidavit_id) {
            $query = $query->where('far_scheme_app_details.affidavit_id', $request->affidavit_id);
        }


        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'License Application list',
            'data' => $list
        ]);
    }

    /**
    * show all agreemented application which status = 12 for participation fee
    */
    public function participationFeeList(Request $request)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->leftjoin('far_scheme_license','far_scheme_application.id', '=','far_scheme_license.scheme_application_id')
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id')
                        ->select('far_scheme_application.*',
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn',
                                'far_scheme_license.license_no',
                            )
                        ->where(function ($query) {
                            return $query->where('far_scheme_application.status', 12)
                                    ->orWhere('far_scheme_application.status', '>', 7);
                        })
                        ->orderBy('far_scheme_application.id','DESC');

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
                $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        }

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        if ($request->far_village) {
            $query = $query->where('far_scheme_application.far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->status) {
            $query = $query->where('far_scheme_application.status', $request->status);
        }

        if ($request->payment_status) {
            $query = $query->where('far_scheme_application.payment_status', $request->payment_status);
        }

        if ($request->sch_man_name) {
            $query = $query->where('far_scheme_app_details.sch_man_name', 'like', "{$request->sch_man_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_name_bn', 'like', "{$request->sch_man_name}%");
        }

        if ($request->sch_man_district_id) {
            $query = $query->where('far_scheme_app_details.sch_man_district_id', $request->sch_man_district_id);
        }

        if ($request->sch_man_division_id) {
            $query = $query->where('far_scheme_app_details.sch_man_division_id', $request->sch_man_division_id);
        }

        if ($request->sch_man_upazilla_id) {
            $query = $query->where('far_scheme_app_details.sch_man_upazilla_id', $request->sch_man_upazilla_id);
        }

        if ($request->sch_man_union_id) {
            $query = $query->where('far_scheme_app_details.sch_man_union_id', $request->sch_man_union_id);
        }

        if ($request->sch_man_village) {
                $query = $query->where('far_scheme_app_details.sch_man_village', 'like', "{$request->sch_man_village}%")
                            ->orWhere('far_scheme_app_details.sch_man_village_bn', 'like', "{$request->sch_man_village}%");
        }

        if ($request->scheme_application_id) {
            $query = $query->where('far_scheme_app_details.scheme_application_id', $request->scheme_application_id);
        }


        if ($request->sch_man_father_name) {
            $query = $query->where('far_scheme_app_details.sch_man_father_name', 'like', "{$request->sch_man_father_name}%")
                        ->orWhere('far_scheme_app_details.sch_man_father_name_bn', 'like', "{$request->sch_man_father_name}%");
        }

        if ($request->sch_man_mother_name) {
            $query = $query->where('far_scheme_app_details.sch_man_mother_name', 'like', "{$request->sch_man_mother_name}%")
                    ->orWhere('far_scheme_app_details.sch_man_mother_name_bn', 'like', "{$request->sch_man_mother_name}%");
        }

        if ($request->sch_man_mobile_no) {
            $query = $query->where('far_scheme_app_details.sch_man_mobile_no', $request->sch_man_mobile_no);
        }

        if ($request->pump_district_id) {
            $query = $query->where('far_scheme_app_details.pump_district_id', $request->pump_district_id);
        }

        if ($request->pump_upazilla_id) {
            $query = $query->where('far_scheme_app_details.pump_upazilla_id', $request->pump_upazilla_id);
        }

        if ($request->pump_union_id) {
            $query = $query->where('far_scheme_app_details.pump_union_id', $request->pump_union_id);
        }

        if ($request->pump_mauza_no) {
            $query = $query->where('far_scheme_app_details.pump_mauza_no', 'like', "{$request->pump_mauza_no}%")
                    ->orWhere('far_scheme_app_details.pump_mauza_no_bn', 'like', "{$request->pump_mauza_no}%");
        }

        if ($request->pump_jl_no) {
            $query = $query->where('far_scheme_app_details.pump_jl_no', 'like', "{$request->pump_jl_no}%")
                    ->orWhere('far_scheme_app_details.pump_jl_no_bn', 'like', "{$request->pump_jl_no}%");
        }

        if ($request->pump_plot_no) {
            $query = $query->where('far_scheme_app_details.pump_plot_no', 'like', "{$request->pump_plot_no}%")
                    ->orWhere('far_scheme_app_details.pump_plot_no_bn', 'like', "{$request->pump_plot_no}%");
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'License Application list',
            'data' => $list
        ]);
    }

    /**
    * show all agreement application which status = 8
    */
    public function agreementApplicationList(Request $request)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->leftjoin('far_scheme_license','far_scheme_application.id', '=','far_scheme_license.scheme_application_id')
                        ->leftjoin('far_scheme_requisitions','far_scheme_application.id', '=','far_scheme_requisitions.scheme_application_id')
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id')
                        ->select('far_scheme_application.*',
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn',
                                'far_scheme_license.license_no',
                                'far_scheme_requisitions.id as requisition_id',
                                'far_scheme_requisitions.status as requisition_status',
                            )
                        ->where('far_scheme_application.status', 8);

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
                $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        }

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        if ($request->far_village) {
            $query = $query->where('far_scheme_application.far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->status) {
            $query = $query->where('far_scheme_application.status', $request->status);
        }

        if ($request->payment_status) {
            $query = $query->where('far_scheme_application.payment_status', $request->payment_status);
        }

        if ($request->sch_man_name) {
            $query = $query->where('far_scheme_app_details.sch_man_name', 'like', "{$request->sch_man_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_name_bn', 'like', "{$request->sch_man_name}%");
        }

        if ($request->sch_man_district_id) {
            $query = $query->where('far_scheme_app_details.sch_man_district_id', $request->sch_man_district_id);
        }

        if ($request->sch_man_division_id) {
            $query = $query->where('far_scheme_app_details.sch_man_division_id', $request->sch_man_division_id);
        }

        if ($request->sch_man_upazilla_id) {
            $query = $query->where('far_scheme_app_details.sch_man_upazilla_id', $request->sch_man_upazilla_id);
        }

        if ($request->sch_man_union_id) {
            $query = $query->where('far_scheme_app_details.sch_man_union_id', $request->sch_man_union_id);
        }

        if ($request->sch_man_village) {
                $query = $query->where('far_scheme_app_details.sch_man_village', 'like', "{$request->sch_man_village}%")
                            ->orWhere('far_scheme_app_details.sch_man_village_bn', 'like', "{$request->sch_man_village}%");
        }

        if ($request->scheme_application_id) {
            $query = $query->where('far_scheme_app_details.scheme_application_id', $request->scheme_application_id);
        }


        if ($request->sch_man_father_name) {
                $query = $query->where('far_scheme_app_details.sch_man_father_name', 'like', "{$request->sch_man_father_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_father_name_bn', 'like', "{$request->sch_man_father_name}%");
        }

        if ($request->sch_man_mother_name) {
                $query = $query->where('far_scheme_app_details.sch_man_mother_name', 'like', "{$request->sch_man_mother_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_mother_name_bn', 'like', "{$request->sch_man_mother_name}%");
        }

        if ($request->sch_man_mobile_no) {
            $query = $query->where('far_scheme_app_details.sch_man_mobile_no', $request->sch_man_mobile_no);
        }

        if ($request->pump_district_id) {
            $query = $query->where('far_scheme_app_details.pump_district_id', $request->pump_district_id);
        }

        if ($request->pump_upazilla_id) {
            $query = $query->where('far_scheme_app_details.pump_upazilla_id', $request->pump_upazilla_id);
        }

        if ($request->pump_union_id) {
            $query = $query->where('far_scheme_app_details.pump_union_id', $request->pump_union_id);
        }

        if ($request->pump_mauza_no) {
                $query = $query->where('far_scheme_app_details.pump_mauza_no', 'like', "{$request->pump_mauza_no}%")
                            ->orWhere('far_scheme_app_details.pump_mauza_no_bn', 'like', "{$request->pump_mauza_no}%");
        }

        if ($request->pump_jl_no) {
                $query = $query->where('far_scheme_app_details.pump_jl_no', 'like', "{$request->pump_jl_no}%")
                            ->orWhere('far_scheme_app_details.pump_jl_no_bn', 'like', "{$request->pump_jl_no}%");
        }

        if ($request->pump_plot_no) {
                $query = $query->where('far_scheme_app_details.pump_plot_no', 'like', "{$request->pump_plot_no}%")
                            ->orWhere('far_scheme_app_details.pump_plot_no_bn', 'like', "{$request->pump_plot_no}%");
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'License Application list',
            'data' => $list
        ]);
    }

    /**
    * show all requisition which application which status = 9
    */
    public function requisitionList(Request $request)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->join('far_scheme_license','far_scheme_application.id', '=','far_scheme_license.scheme_application_id')
                        ->leftjoin('far_scheme_requisitions','far_scheme_application.id', '=','far_scheme_requisitions.scheme_application_id')
                        ->leftjoin('far_scheme_supply_equipments','far_scheme_requisitions.id', '=','far_scheme_supply_equipments.requisition_id')
                        ->select('far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_license.license_no',
                                'far_scheme_requisitions.id',
                                'far_scheme_requisitions.requisition_id',
                                'far_scheme_application.application_id as application_id',
                                'far_scheme_application.org_id',
                                'far_scheme_application.id as scheme_application_id',
                                'far_scheme_supply_equipments.id as supply_note_id',
                                'far_scheme_supply_equipments.supply_note',
                                'far_scheme_supply_equipments.supply_note_bn',
                            )
                        ->where('far_scheme_requisitions.id','!=', null)
                        ->where('far_scheme_application.status', '>=', 9);

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }
          
        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'License Application list',
            'data' => $list
        ]);
    }

    /**
    * show all agreement application which status = 10
    */
    public function pumpInstallList(Request $request)
    {
        $query = DB::table('far_scheme_application')
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')
                        ->leftjoin('far_scheme_license','far_scheme_application.id', '=','far_scheme_license.scheme_application_id')
                        ->leftjoin('far_scheme_requisitions','far_scheme_application.id', '=','far_scheme_requisitions.scheme_application_id')
                        ->leftjoin('master_pump_types','far_scheme_requisitions.pump_type_id', '=','master_pump_types.id')
                        ->select('far_scheme_application.id', 'far_scheme_application.application_id',
                                'far_scheme_application.status','far_scheme_application.application_type_id',
                                'far_scheme_app_details.sch_man_name','far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_license.license_no',
                                'master_pump_types.pump_type_name',
                                'master_pump_types.pump_type_name_bn',
                                'far_scheme_requisitions.pump_type_id'
                            )
                        ->where(function($query) {
                            return $query->where('far_scheme_application.status', 10)
                                        ->orWhere('far_scheme_application.status', 11);
                        });

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
                $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        }

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        if ($request->far_village) {
            $query = $query->where('far_scheme_application.far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->status) {
            $query = $query->where('far_scheme_application.status', $request->status);
        }

        if ($request->payment_status) {
            $query = $query->where('far_scheme_application.payment_status', $request->payment_status);
        }

        if ($request->sch_man_name) {
            $query = $query->where('far_scheme_app_details.sch_man_name', 'like', "{$request->sch_man_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_name_bn', 'like', "{$request->sch_man_name}%");
        }

        if ($request->sch_man_district_id) {
            $query = $query->where('far_scheme_app_details.sch_man_district_id', $request->sch_man_district_id);
        }

        if ($request->sch_man_division_id) {
            $query = $query->where('far_scheme_app_details.sch_man_division_id', $request->sch_man_division_id);
        }

        if ($request->sch_man_upazilla_id) {
            $query = $query->where('far_scheme_app_details.sch_man_upazilla_id', $request->sch_man_upazilla_id);
        }

        if ($request->sch_man_union_id) {
            $query = $query->where('far_scheme_app_details.sch_man_union_id', $request->sch_man_union_id);
        }

        if ($request->sch_man_village) {
                $query = $query->where('far_scheme_app_details.sch_man_village', 'like', "{$request->sch_man_village}%")
                            ->orWhere('far_scheme_app_details.sch_man_village_bn', 'like', "{$request->sch_man_village}%");
        }

            if ($request->scheme_application_id) {
            $query = $query->where('far_scheme_app_details.scheme_application_id', $request->scheme_application_id);
        }


        if ($request->sch_man_father_name) {
                $query = $query->where('far_scheme_app_details.sch_man_father_name', 'like', "{$request->sch_man_father_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_father_name_bn', 'like', "{$request->sch_man_father_name}%");
        }

        if ($request->sch_man_mother_name) {
                $query = $query->where('far_scheme_app_details.sch_man_mother_name', 'like', "{$request->sch_man_mother_name}%")
                            ->orWhere('far_scheme_app_details.sch_man_mother_name_bn', 'like', "{$request->sch_man_mother_name}%");
        }

        if ($request->sch_man_mobile_no) {
            $query = $query->where('far_scheme_app_details.sch_man_mobile_no', $request->sch_man_mobile_no);
        }

        if ($request->pump_district_id) {
            $query = $query->where('far_scheme_app_details.pump_district_id', $request->pump_district_id);
        }

        if ($request->pump_upazilla_id) {
            $query = $query->where('far_scheme_app_details.pump_upazilla_id', $request->pump_upazilla_id);
        }

        if ($request->pump_union_id) {
            $query = $query->where('far_scheme_app_details.pump_union_id', $request->pump_union_id);
        }

        if ($request->pump_mauza_no) {
                $query = $query->where('far_scheme_app_details.pump_mauza_no', 'like', "{$request->pump_mauza_no}%")
                            ->orWhere('far_scheme_app_details.pump_mauza_no_bn', 'like', "{$request->pump_mauza_no}%");
        }

        if ($request->pump_jl_no) {
                $query = $query->where('far_scheme_app_details.pump_jl_no', 'like', "{$request->pump_jl_no}%")
                            ->orWhere('far_scheme_app_details.pump_jl_no_bn', 'like', "{$request->pump_jl_no}%");
        }

        if ($request->pump_plot_no) {
                $query = $query->where('far_scheme_app_details.pump_plot_no', 'like', "{$request->pump_plot_no}%")
                            ->orWhere('far_scheme_app_details.pump_plot_no_bn', 'like', "{$request->pump_plot_no}%");
        }

        if ($request->general_minutes) {
            $query = $query->where('pump_plot_no.general_minutes', $request->general_minutes);
        }

        if ($request->scheme_lands) {
            $query = $query->where('pump_plot_no.scheme_lands', $request->scheme_lands);
        }

            if ($request->scheme_map) {
            $query = $query->where('far_scheme_app_details.scheme_map', $request->scheme_map);
        }

        if ($request->affidavit_id) {
            $query = $query->where('far_scheme_app_details.affidavit_id', $request->affidavit_id);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'License Application list',
            'data' => $list
        ]);
    }

    /* Updates total farmer of scheme application */
    public function updateFarmer (Request $request, $id) {
        if (!$request->total_farmer) {
            return response([
                'success' => false,
                'message' => 'Please insert a valid number'
            ]);
        }
        $totalFarmer = $request->total_farmer;
        $query = DB::table('far_scheme_application')->where('status', 14)->where('id', $id);
        $app = $query->first();

        if ($app) {
            if ((int)$totalFarmer < $app->added_farmer && (int)$totalFarmer < $app->total_farmer) {
                return response([
                    'success' => false,
                    'message' => 'Please remove some farmers before updating.'
                ]);
            } else {
                $query->update(['total_farmer' => $request->total_farmer]);

                return response([
                    'success' => true,
                    'message' => 'Updated total number of farmers.',
                    'data' => []
                ]);
            }
        }
        return response([
            'success' => false,
            'message' => 'Unauthorized! Pump has not been installed yet, you cannot customize this.'
        ]);
    }

    public function removeFarmer (Request $request, $id) {
        if ($request->application_id && $id) {
            $query = DB::table('far_scheme_application')->where('id', $request->application_id);
            $firstItem = $query->first();

            if ($query->update(['added_farmer' => $firstItem->added_farmer - 1])) {
                DB::table('farmer_land_details')->where('id', $id)->delete();
            }

            return response([
                'success' => true,
                'message' => 'Updated total number of farmers.',
                'data' => []
            ]);
        }

        return response([
            'success' => false,
            'message' => 'Invalid data parameter.'
        ]);
    }
}
