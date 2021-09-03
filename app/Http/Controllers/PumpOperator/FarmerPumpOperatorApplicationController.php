<?php
namespace App\Http\Controllers\PumpOperator;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use App\Models\PumpOperator\FarmerPumpOperatorApplication;
use App\Models\PumpOperator\FarmerPumpOperatorDocuments;
use App\Http\Validations\PumpOperator\FarmerPumpOperatorApplicationValidations;
use App\Models\PumpInfoManagement\PumpOperator;
use App\Library\RestService;
use App\Models\FarmerProfile\FarmerBasicInfos;
use App\Models\PumpOperator\FarmerPumpOperatorApplicationReniews;
use App\Models\PumpOperator\PumpOptAppsApproval;
use App\Models\PumpOperator\PumpOptAppsSurvey;

class FarmerPumpOperatorApplicationController extends Controller
{
     /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      // dd('hello');
    }

    /**
     * get all Pump Operator Application
     */
    public function index(Request $request)
    {
        $query = DB::table('far_pump_opt_apps')
                        ->leftjoin('pump_informations','far_pump_opt_apps.pump_id','pump_informations.id')
                        ->select('far_pump_opt_apps.*',
                            'pump_informations.division_id as pump_division_id',
                            'pump_informations.district_id as pump_district_id',
                            'pump_informations.upazilla_id as pump_upazilla_id',
                            'pump_informations.union_id as pump_union_id',
                            'pump_informations.mouza_no as pump_mouza_no',
                            'pump_informations.jl_no as pump_jl_no',
                            'pump_informations.plot_no as pump_plot_no'
                        )
                        ->where('far_pump_opt_apps.payment_status', '>=', 1)
                        ->where('far_pump_opt_apps.status', '>=', 2)
                        ->where('far_pump_opt_apps.is_renew', 0) // 0 mean new application
                        ->orderBy('far_pump_opt_apps.id', 'DESC');


        if ($request->mouza_no) {
            $query->where('pump_informations.mouza_no', $request->mouza_no);
        }
        if ($request->plot_no) {
            $query->where('pump_informations.plot_no', $request->plot_no);
        }
        if ($request->jl_no) {
            $query->where('pump_informations.jl_no', $request->jl_no);
        }
        if ($request->far_division_id) {
            $query->where('pump_informations.division_id', $request->far_division_id);
        }
        if ($request->far_district_id) {
            $query->where('pump_informations.district_id', $request->far_district_id);
        }
        if ($request->far_upazilla_id) {
            $query->where('pump_informations.upazilla_id', $request->far_upazilla_id);
        }

        if ($request->receiver_id) {
            $query->where('pum_opt_apps_approvals.receiver_id', $request->receiver_id);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        if(count($list) > 0 ){
            return response([
                'success' => true,
                'message' => 'Farmer pump operator application list',
                'data' => $list
            ]);

        }else{
            return response([
                'success' => false,
                'message' => 'Data not found!!',
                'data' => $list
            ]);
        }
    }

    /**
     * receive Pump Operator Application
     */
    public function receivePumpOptApplication(Request $request)
    {
        $query = DB::table('far_pump_opt_apps')
                        ->leftjoin('pump_informations','far_pump_opt_apps.pump_id','pump_informations.id')
                        ->leftjoin('pum_opt_apps_approvals','far_pump_opt_apps.id','pum_opt_apps_approvals.pump_opt_apps_id')
                        ->select('far_pump_opt_apps.*',
                            'pump_informations.division_id as pump_division_id',
                            'pump_informations.district_id as pump_district_id',
                            'pump_informations.upazilla_id as pump_upazilla_id',
                            'pump_informations.union_id as pump_union_id',
                            'pump_informations.mouza_no as pump_mouza_no',
                            'pump_informations.jl_no as pump_jl_no',
                            'pump_informations.plot_no as pump_plot_no'
                        )
                        ->where(function($q){
                            return $q->where('far_pump_opt_apps.status', 5)
                                    ->orWhere('far_pump_opt_apps.status','!=', 3);
                        })
                        ->where('pum_opt_apps_approvals.receiver_id', $request->receiver_id)
                        ->where('pum_opt_apps_approvals.status', 1)
                        ->orderBy('far_pump_opt_apps.id', 'DESC');


        if ($request->mouza_no) {
            $query->where('pump_informations.mouza_no', $request->mouza_no);
        }
        if ($request->plot_no) {
            $query->where('pump_informations.plot_no', $request->plot_no);
        }
        if ($request->jl_no) {
            $query->where('pump_informations.jl_no', $request->jl_no);
        }
        if ($request->far_division_id) {
            $query->where('pump_informations.division_id', $request->far_division_id);
        }
        if ($request->far_district_id) {
            $query->where('pump_informations.district_id', $request->far_district_id);
        }
        if ($request->far_upazilla_id) {
            $query->where('pump_informations.upazilla_id', $request->far_upazilla_id);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        if(count($list) > 0 ){
            return response([
                'success' => true,
                'message' => 'Farmer pump operator application list',
                'data' => $list
            ]);

        }else{
            return response([
                'success' => false,
                'message' => 'Data not found!!',
                'data' => $list
            ]);
        }
    }

    /**
     * survey Pump Operator Application
     */
    public function surveyPumpOptApplication(Request $request)
    {
        $query = DB::table('far_pump_opt_apps')
                        ->leftjoin('pum_opt_apps_approvals','far_pump_opt_apps.id','pum_opt_apps_approvals.pump_opt_apps_id')
                        ->leftjoin('pump_informations','far_pump_opt_apps.pump_id','pump_informations.id')
                        ->leftjoin('pump_opt_apps_surveys','far_pump_opt_apps.id','pump_opt_apps_surveys.pump_opt_apps_id')
                        ->select('far_pump_opt_apps.*',
                            'pum_opt_apps_approvals.id as approval_id',
                            'pum_opt_apps_approvals.status as approval_status',
                            'pum_opt_apps_approvals.for as approval_for',
                            'pum_opt_apps_approvals.receiver_id as receiver_id',
                            'pump_informations.division_id as pump_division_id',
                            'pump_informations.district_id as pump_district_id',
                            'pump_informations.upazilla_id as pump_upazilla_id',
                            'pump_informations.union_id as pump_union_id',
                            'pump_informations.mouza_no as pump_mouza_no',
                            'pump_informations.jl_no as pump_jl_no',
                            'pump_informations.plot_no as pump_plot_no',
                            'pump_opt_apps_surveys.id as survey_id'
                        )
                        ->where('far_pump_opt_apps.status', 6)
                        ->where('pum_opt_apps_approvals.receiver_id', $request->receiver_id)
                        ->where('pum_opt_apps_approvals.for', 2)
                        ->where('pum_opt_apps_approvals.status', 1)
                        ->orderBy('far_pump_opt_apps.id', 'DESC');
      
        if ($request->mouza_no) {
            $query->where('pump_informations.mouza_no', $request->mouza_no);
        }
        if ($request->plot_no) {
            $query->where('pump_informations.plot_no', $request->plot_no);
        }
        if ($request->jl_no) {
            $query->where('pump_informations.jl_no', $request->jl_no);
        }
        if ($request->far_division_id) {
            $query->where('pump_informations.division_id', $request->far_division_id);
        }
        if ($request->far_district_id) {
            $query->where('pump_informations.district_id', $request->far_district_id);
        }
        if ($request->far_upazilla_id) {
            $query->where('pump_informations.upazilla_id', $request->far_upazilla_id);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        if(count($list) > 0 ){
            return response([
                'success' => true,
                'message' => 'Farmer pump operator application list',
                'data' => $list
            ]);

        }else{
            return response([
                'success' => false,
                'message' => 'Data not found!!',
                'data' => $list
            ]);
        }
    }

    /**
     * get all Pump Renew application
     */
    public function getRenewList(Request $request)
    {
        $query = DB::table('far_pump_opt_app_reniews')
                ->join('far_pump_opt_apps','far_pump_opt_app_reniews.pump_opt_apps_id','=','far_pump_opt_apps.id')
                ->leftjoin('pum_opt_apps_approvals','far_pump_opt_apps.id','pum_opt_apps_approvals.pump_opt_apps_id')
                ->leftjoin('pump_informations','far_pump_opt_apps.pump_id','pump_informations.id')
                ->leftjoin('pump_opt_apps_surveys','far_pump_opt_apps.id','pump_opt_apps_surveys.pump_opt_apps_id')
                ->select('far_pump_opt_apps.*',
                        'pum_opt_apps_approvals.id as approval_id',
                        'pum_opt_apps_approvals.status as approval_status',
                        'pum_opt_apps_approvals.receiver_id as receiver_id',
                        'pump_informations.division_id as pump_division_id','pump_informations.district_id as pump_district_id',
                        'pump_informations.upazilla_id as pump_upazilla_id','pump_informations.union_id as pump_union_id',
                        'pump_informations.mouza_no as pump_mouza_no', 'pump_informations.jl_no as pump_jl_no',
                        'pump_informations.plot_no as pump_plot_no', 'pump_opt_apps_surveys.id as survey_id'
                )
                ->where('far_pump_opt_apps.payment_status', '=', 1)
                ->where('far_pump_opt_apps.is_renew', 1)
                ->where('far_pump_opt_apps.status', '>=', 2);

        if ($request->mouza_no) {
            $query->where('pump_informations.mouza_no', $request->mouza_no);
        }
        if ($request->plot_no) {
            $query->where('pump_informations.plot_no', $request->plot_no);
        }
        if ($request->jl_no) {
            $query->where('pump_informations.jl_no', $request->jl_no);
        }
        if ($request->far_division_id) {
            $query->where('pump_informations.division_id', $request->far_division_id);
        }
        if ($request->far_district_id) {
            $query->where('pump_informations.district_id', $request->far_district_id);
        }
        if ($request->far_upazilla_id) {
            $query->where('pump_informations.upazilla_id', $request->far_upazilla_id);
        }
        if ($request->jl_no) {
            $query->where('pump_informations.jl_no', $request->jl_no);
        }


        $list = $query->paginate(request('per_page', config('app.per_page')));

        if(count($list) > 0 ){
            return response([
                'success' => true,
                'message' => 'Farmer pump operator renew application list',
                'data' => $list
            ]);

        }else{
            return response([
                'success' => false,
                'message' => 'Data not found!!',
                'data' => $list
            ]);
        }
    }

    /**
     * get all Application Details List
     */
    public function getApplicationDetails($id)
    {
        $application = DB::table('far_pump_opt_apps')
                            ->leftjoin('pump_informations','far_pump_opt_apps.pump_id','pump_informations.id')
                            ->select( "far_pump_opt_apps.*", "pump_informations.pump_id")
                            ->select('far_pump_opt_apps.*',
                                'pump_informations.division_id as pump_division_id',
                                'pump_informations.district_id as pump_district_id',
                                'pump_informations.upazilla_id as pump_upazilla_id',
                                'pump_informations.union_id as pump_union_id',
                                'pump_informations.mouza_no as pump_mouza_no',
                                'pump_informations.jl_no as pump_jl_no',
                                'pump_informations.plot_no as pump_plot_no',
                                'pump_informations.pump_id as pump_info_pump_id'
                            )
                            ->where('far_pump_opt_apps.id', $id)
                            ->first();

        $attachments = DB::table('far_pump_opt_docs')
                        ->select( "far_pump_opt_docs.*")
                        ->where('pump_opt_apps_id', $id)
                        ->get();

        $surveys    = PumpOptAppsSurvey::where('pump_opt_apps_id', $id)->get()->toArray();
        $approvals  = PumpOptAppsApproval::where('pump_opt_apps_id', $id)->get()->toArray();

        $sender_ids = array_column($approvals, 'sender_id'); 
        $receiver_ids = array_column($approvals, 'receiver_id'); 
        $survey_user_ids = array_column($surveys, 'user_id'); 

        $user_ids = array_merge(array_merge($sender_ids,$receiver_ids), $survey_user_ids); 
        $user_ids = array_unique($user_ids); 

        $baseUrl = config('app.base_url.auth_service');
        $uri = '/user/user-list';

        $users = RestService::getData($baseUrl, $uri, ['user_ids' => $user_ids]);
        $users = json_decode($users, true); 

        if($application != null ){
            return response([
                'success'       => true,
                'message'       => 'Application Details',
                'data'          => $application,
                'attachments'   => $attachments,
                'surveys'       =>  $surveys,
                'approvals'     =>  $approvals,
                'users'         =>  $users
            ]);

        }else{
            return response([
                'success' => false,
                'message' => 'Data not found!!'
            ]);
        }
    }

    public function singleDetails($id) {
        return FarmerPumpOperatorApplication::find($id)->with('pump_opt_documents')->first();
    }

    public function single_index(Request $request)
    {
        $user_id = (int)user_id();
        $query = FarmerPumpOperatorApplication::with(['pump_opt_documents', 'pump_information' => function($q) {
            $q->select('id', 'pump_id');
        }])
        ->leftjoin('far_pump_opt_app_reniews','far_pump_opt_apps.id','far_pump_opt_app_reniews.pump_opt_apps_id')
        ->leftjoin('pump_informations','far_pump_opt_apps.pump_id','pump_informations.id')
        ->select('far_pump_opt_apps.*','far_pump_opt_app_reniews.payment_status as renew_payment_status',
        'far_pump_opt_app_reniews.application_date as renew_application_date',
        'far_pump_opt_app_reniews.status as renew_status',
        'pump_informations.division_id as pump_division_id','pump_informations.district_id as pump_district_id',
        'pump_informations.upazilla_id as pump_upazilla_id','pump_informations.union_id as pump_union_id',
        'pump_informations.mouza_no as pump_mouza_no', 'pump_informations.jl_no as pump_jl_no', 
        'pump_informations.plot_no as pump_plot_no'
        );
        $query = $query->where('farmer_id', $user_id);
        $query = $query->orderBy('far_pump_opt_apps.id', 'asc');
        $list = $query->paginate(request('per_page', config('app.per_page')));

        if(count($list) >=1 ){
            return response([
                'success' => true,
                'message' => 'Farmer pump operator opplication list',
                'data' => $list
            ]);

        }else{
            return response([
                'success' => true,
                'message' => 'Data not found!!',
                'data' => $list
            ]);
        }

    }

    /**
     * Pump Operator Application & Pump Operator Application details  store
     */
    public function store(Request $request)
    {  
        $validationResult = FarmerPumpOperatorApplicationValidations::validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $getLastIdSerial = DB::table('far_pump_opt_apps')
                                    ->select('id_serial')
                                    ->orderBy('id','desc')
                                    ->first();

        if ($getLastIdSerial !=null) {
            $idSerial = $getLastIdSerial->id_serial;
            $idSerial = $idSerial + 1;
            $applicationId = "poappid#".$idSerial;
        } else {
            $idSerial = 1000;
            $applicationId = "poappid#".$idSerial;
        }

        $applicant_photo_path   = 'pump-operator-application/applicant-photo';
        $applicant_photo        =  $request->file('applicant_photo') ? $request->file('applicant_photo') : null;

        if($applicant_photo != null && $applicant_photo != ""){
            $applicant_photo_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, null, 'applicant_photo');
        }else{
            $applicant_photo_name = null;  
        }

        DB::beginTransaction();

        try {
            $fpopapp                       = new FarmerPumpOperatorApplication();
            $fpopapp->farmer_id            = (int)user_id();
            $fpopapp->email                = username();
            $fpopapp->org_id               = (int)$request->org_id;
            $fpopapp->pump_id              = (int)$request->pump_id;
            $fpopapp->name                 = $request->name;
            $fpopapp->name_bn              = $request->name_bn;
            $fpopapp->gender               = $request->gender;
            $fpopapp->father_name          = $request->father_name;
            $fpopapp->father_name_bn       = $request->father_name_bn;
            $fpopapp->mother_name          = $request->mother_name;
            $fpopapp->mother_name_bn       = $request->mother_name_bn;
            $fpopapp->nid                  = $request->nid;
            $fpopapp->far_mobile_no        = $request->far_mobile_no;
            $fpopapp->far_division_id      = (int)$request->far_division_id;
            $fpopapp->far_district_id      = (int)$request->far_district_id;
            $fpopapp->far_upazilla_id      = (int)$request->far_upazilla_id;
            $fpopapp->far_union_id         = (int)$request->far_union_id;
            $fpopapp->office_id            = (int)$request->office_id;
            $fpopapp->far_village          = $request->far_village;
            $fpopapp->far_village_bn       = $request->far_village_bn;
            $fpopapp->date_of_birth        = $request->date_of_birth;
            $fpopapp->qualification        = $request->qualification;
            $fpopapp->payment_status       = $request->payment_status;
            $fpopapp->final_approve        = $request->final_approve;
            $fpopapp->application_id       = $applicationId;
            $fpopapp->id_serial            = $idSerial;
            $fpopapp->applicant_photo      = $applicant_photo_name;

            if ($request->is_renew) {
                $fpopapp->is_renew         = 1;
            }

            if($applicant_photo != null && $applicant_photo != ""){
                GlobalFileUploadFunctoin::file_upload($request, $applicant_photo_path, 'applicant_photo', $applicant_photo_name );
            }

            $fpopapp->save();

            // delete previous added documents
            DB::table('far_pump_opt_docs')
                        ->where('user_id', $request->farmer_id)
                        ->where('pump_opt_apps_id', '=', null)
                        ->whereDate('created_at', '<', date('Y-m-d'))
                        ->delete();

            $documents = DB::table('far_pump_opt_docs')
                            ->where('user_id', $request->farmer_id)
                            ->where('pump_opt_apps_id', '=', null)
                            ->whereDate('created_at', '=', date('Y-m-d'))
                            ->get();

            if (count($documents) > 0) {
                foreach ($documents as $document) {

                    $tmpDoc = FarmerPumpOperatorDocuments::find($document->id);
                    $tmpDoc->pump_opt_apps_id = $fpopapp->id;
                    $tmpDoc->update();
                }
            }

            if ($request->application_type == 2) {

                $appRenew = new FarmerPumpOperatorApplicationReniews();
                $appRenew->pump_opt_apps_id = $fpopapp->id;
                $appRenew->application_date = date('Y-m-d');
                $appRenew->save();

                save_log([
                    'data_id'    => $appRenew->id,
                    'table_name' => 'far_pump_opt_app_reniews',
                ]);
            }

            save_log([
                'data_id' => $fpopapp->id,
                'table_name' => 'far_pump_opt_apps',
            ]);

            DB::commit();


        } catch (\Exception $ex) {
            DB::rollback();
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'New Pump Operator Application',
            'data' => $fpopapp
        ]);

    }

    /**
     * Pump Operator Application update
     */
    public function update(Request $request, $id)
    {
        $validationResult = FarmerPumpOperatorApplicationValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $fpopapp = FarmerPumpOperatorApplication::find($id);

        if (!$fpopapp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }
        DB::beginTransaction();

        try {

            $fpopapp->farmer_id            = (int)user_id();
            $fpopapp->email                = username();
            $fpopapp->org_id               = (int)$request->org_id;
            $fpopapp->pump_id              = (int)$request->pump_id;
            $fpopapp->name                 = $request->name;
            $fpopapp->name_bn              = $request->name_bn;
            $fpopapp->gender               = $request->gender;
            $fpopapp->father_name          = $request->father_name;
            $fpopapp->father_name_bn       = $request->father_name_bn;
            $fpopapp->mother_name          = $request->mother_name;
            $fpopapp->mother_name_bn       = $request->mother_name_bn;
            $fpopapp->nid                  = $request->nid;
            $fpopapp->far_mobile_no        = $request->far_mobile_no;
            $fpopapp->far_division_id      = (int)$request->far_division_id;
            $fpopapp->far_district_id      = (int)$request->far_district_id;
            $fpopapp->far_upazilla_id      = (int)$request->far_upazilla_id;
            $fpopapp->far_union_id         = (int)$request->far_union_id;
            $fpopapp->office_id             = (int)$request->office_id;
            $fpopapp->far_village          = $request->far_village;
            $fpopapp->far_village_bn       = $request->far_village_bn;
            $fpopapp->date_of_birth        = $request->date_of_birth;
            $fpopapp->qualification        = $request->qualification;
            $fpopapp->payment_status       = $request->payment_status;
            $fpopapp->final_approve        = $request->final_approve;
            $fpopapp->update();

            save_log([
                'data_id' => $fpopapp->id,
                'table_name' => 'far_pump_opt_apps',
                'execution_type' => 1
            ]);

            DB::commit();

        } catch (\Exception $ex) {
            DB::rollback();
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $fpopapp
        ]);

    }

    /**
     * Pump Operator Application status update
     */
    public function updateAsProcessing($id)
    {
        $fpopapp = FarmerPumpOperatorApplication::find($id);

        if (!$fpopapp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $fpopapp->status = 1;
        $fpopapp->update();

        save_log([
            'data_id' => $fpopapp->id,
            'table_name' => 'far_pump_opt_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Application statas update as processing',
            'data'    => $fpopapp
        ]);
    }

    /**
     * Pump Operator Application status approve
     */
    public function updateAsApproved($id)
    {
        $fpopapp = FarmerPumpOperatorApplication::find($id);

        if (!$fpopapp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $existPumpOperator = PumpOperator::where('pump_operator_user_id', $fpopapp->farmer_id)
                                            ->where('pump_id','!=', $fpopapp->pump_id)
                                            ->first();

        if ($existPumpOperator == null) {

            $pumpOperator                       = new PumpOperator();
            $pumpOperator->org_id               = $fpopapp->org_id;
            $pumpOperator->pump_id              = $fpopapp->pump_id;
            $pumpOperator->name                 = $fpopapp->name;
            $pumpOperator->name_bn              = $fpopapp->name_bn;
            $pumpOperator->father_name          = $fpopapp->father_name;
            $pumpOperator->father_name_bn       = $fpopapp->father_name_bn;
            $pumpOperator->gender               = $fpopapp->gender;
            $pumpOperator->mother_name          = $fpopapp->mother_name;
            $pumpOperator->mother_name_bn       = $fpopapp->mother_name_bn;
            $pumpOperator->nid                  = $fpopapp->nid;
            $pumpOperator->village_name         = $fpopapp->far_village;
            $pumpOperator->village_name_bn      = $fpopapp->far_village_bn;
            $pumpOperator->mobile_no            = $fpopapp->far_mobile_no;
            $pumpOperator->email                = $fpopapp->email;
            $pumpOperator->pump_operator_user_id    = $fpopapp->farmer_id;
            $pumpOperator->pump_operator_username   = $fpopapp->far_mobile_no;
            $pumpOperator->pump_operator_email      = $fpopapp->email;
            $pumpOperator->created_by           = (int)user_id();
            $pumpOperator->updated_by           = (int)user_id();
            $pumpOperator->save();

            save_log([
                'data_id'   => $pumpOperator->id,
                'table_name'=> 'pump_operators'
            ]);
        }

        $existApprovalStatusPending = PumpOptAppsApproval::where('pump_opt_apps_id', $id)->where('status', 1)->first();

        if($existApprovalStatusPending != null) {
            $existApprovalStatusPending->status = 2;
            $existApprovalStatusPending->update();
        }

        $fpopapp->status = 3;
        $fpopapp->update();

        save_log([
            'data_id' => $fpopapp->id,
            'table_name' => 'far_pump_opt_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Application statas update as approved',
            'data'    => $fpopapp
        ]);
    }

    /**
     * Pump Operator Application Receive
     */
    public function receive($id)
    {
        $fpopapp = FarmerPumpOperatorApplication::find($id);

        if (!$fpopapp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $pumpOptApproval = PumpOptAppsApproval::where('pump_opt_apps_id', $id)->where('status', 1)->first();

        if($pumpOptApproval != null) {
            $pumpOptApproval->status = 2;
            $pumpOptApproval->update();
        }

        $fpopapp->status = $pumpOptApproval->for === 1 ? 7 : 8;
        $fpopapp->update();

        save_log([
            'data_id'       => $fpopapp->id,
            'table_name'    => 'far_pump_opt_apps',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Application statas update as approved',
            'data'    => $fpopapp
        ]);
    }

    /**
     * Pump Operator Application send
     */
    public function send(Request $request)
    {
        try {

            $pumpOptApprove = PumpOptAppsApproval::where('receiver_id', $request->sender_id)->where('for', 1)->where('status', 1)->first();
            if ($pumpOptApprove != null) {
                $pumpOptApprove->status = 2;
                $pumpOptApprove->update();
            }

            $approval                   = new PumpOptAppsApproval();
            $approval->pump_opt_apps_id = (int)$request->pump_opt_apps_id;
            $approval->sender_id        = (int)$request->sender_id;
            $approval->receiver_id      = (int)$request->receiver_id;
            $approval->note             = $request->note;
            $approval->note_bn          = $request->note_bn;
            $approval->for              = $request->for;
            $approval->save();

            save_log([
                'data_id'    => $approval->id,
                'table_name' => 'pum_opt_apps_approvals',
            ]);

            $fpopapp = FarmerPumpOperatorApplication::find($request->pump_opt_apps_id);
            $fpopapp->status = 5; // 5 mean send
            $fpopapp->update();

        } catch (\Exception $ex) {

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Forward Successfully',
            'data'    => $approval
        ]);
    }

    /**
     * Pump Operator Application send for survey
     */
    public function sendForSurvey(Request $request)
    {
        try {
            $approval                   = new PumpOptAppsApproval();
            $approval->pump_opt_apps_id = (int)$request->pump_opt_apps_id;
            $approval->sender_id        = (int)$request->sender_id;
            $approval->receiver_id      = (int)$request->receiver_id;
            $approval->note             = $request->note;
            $approval->note_bn          = $request->note_bn;
            $approval->for              = $request->for;
            $approval->save();

            save_log([
                'data_id'    => $approval->id,
                'table_name' => 'pum_opt_apps_approvals',
            ]);

            $fpopapp = FarmerPumpOperatorApplication::find($request->pump_opt_apps_id);
            $fpopapp->status = 6; // 6 mean send for survey
            $fpopapp->update();

        } catch (\Exception $ex) {

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Forward Successfully',
            'data'    => $approval
        ]);
    }

    /**
     * Pump Operator Application reject
     */
    public function updateAsReject(Request $request)
    {
        $fpopapp = FarmerPumpOperatorApplication::find($request->id);

        if (!$fpopapp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $fpopapp->status = 1;
        $fpopapp->payment_status = 0;
        $fpopapp->update();

        save_log([
            'data_id' => $fpopapp->id,
            'table_name' => 'far_pump_opt_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Application statas update as reject',
            'data'    => $fpopapp
        ]);
    }

    /**
     * Add survey report
     */
    public function addSurvey(Request $request)
    {
        DB::beginTransaction();

        try {

            $pumpOptApprove = PumpOptAppsApproval::where('receiver_id', $request->user_id)->where('for', 2)->where('status', 1)->first();
            if ($pumpOptApprove != null) {
                $pumpOptApprove->status = 2;
                $pumpOptApprove->update();
            }

            $survey                   = new PumpOptAppsSurvey();
            $survey->pump_opt_apps_id = (int)$request->pump_opt_apps_id;
            $survey->user_id          = (int)$request->user_id;
            $survey->survey_date      = date('Y-m-d', strtotime($request->survey_date));
            $survey->note             = $request->note;
            $survey->note_bn          = $request->note_bn;
            $survey->save();

            $fpopapp = FarmerPumpOperatorApplication::find($request->pump_opt_apps_id);
            $fpopapp->status = 8; // 8 mean survey submitted
            $fpopapp->update();

            save_log([
                'data_id'    => $survey->id,
                'table_name' => 'pump_opt_apps_surveys',
            ]);

            DB::commit();

        } catch (\Exception $ex) {

            DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Survey Added Successfully',
            'data'    => $survey
        ]);
    }

    /**
     * send & survey note
     */
    public function sendSurveyNote ($id)
    {
        $surveys    = PumpOptAppsSurvey::where('pump_opt_apps_id', $id)->get()->toArray();
        $approvals  = PumpOptAppsApproval::where('pump_opt_apps_id', $id)->get()->toArray();

        $sender_ids = array_column($approvals, 'sender_id'); 
        $receiver_ids = array_column($approvals, 'receiver_id'); 
        $survey_user_ids = array_column($surveys, 'user_id'); 

        $user_ids = array_merge(array_merge($sender_ids,$receiver_ids), $survey_user_ids); 
        $user_ids = array_unique($user_ids); 

        $baseUrl = config('app.base_url.auth_service');
        $uri = '/user/user-list';

        $users = RestService::getData($baseUrl, $uri, ['user_ids' => $user_ids]);
        $users = json_decode($users, true); 

        if (count($surveys) == 0 && count($approvals) == 0) {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        } else {
            return response([
                'success'   => true,
                'surveys'   =>  $surveys,
                'approvals' =>  $approvals,
                'users'     =>  $users
            ]);
        }
    }

    /**
     * Pump Operator Application destroy
     */
    public function destroy($id)
    {
        $fpopapp = FarmerPumpOperatorApplication::find($id);

        if (!$fpopapp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $fpopapp->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_pump_opt_apps',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    public function getInfo($id) {
        $response = DB::table('pump_operators')->where('pump_operator_user_id', $id)->first();
        if ($response) {
            return response([
                'success' => true,
                'message' => 'Pump operator info.',
                'data' => $response
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Pump operator info not found.'
            ]);
        }
    }
}
