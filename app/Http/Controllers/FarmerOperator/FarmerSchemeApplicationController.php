<?php
namespace App\Http\Controllers\FarmerOperator;

use App\Http\Controllers\Controller;
use App\Http\Validations\FarmerOperator\FarmerSchemeApplicationValidation;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\FarmerOperator\FarmerSchemeApplicationDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\GlobalFileUploadFunctoin;
use Illuminate\Support\Facades\File;
use App\Http\Validations\FarmerOperator\PaymentValidation;
use App\Library\EkpayLibrary;
use App\Models\Payment\IrrigationPayment;
use App\Models\FarmerProfile\FarmerBasicInfos;
use App\Models\PumpInstallation\FarSchemeDocs;
use Illuminate\Support\Facades\Validator;

class FarmerSchemeApplicationController extends Controller
{
    /*
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
      // dd('hello');
    }


    /*
     * get all Farmer Scheme Application 
    */
    public function index(Request $request)
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
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_latitude',
                                'far_scheme_app_details.pump_longitude',
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
                                'master_scheme_types.scheme_type_name_bn');    

        
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
             $query = $query->where('far_scheme_application.farmer_id', 'like', "{$request->farmer_id}%")
                            ->orWhere('far_scheme_app_details.farmer_id', 'like', "{$request->farmer_id}%");
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
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }

    public function single_index(Request $request)
    {
        $user_id = user_id();
        $query = DB::table('far_scheme_application')                          
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id') 
                        ->join('master_scheme_types','far_scheme_application.scheme_type_id', '=','master_scheme_types.id') 
                        ->select(
                                'far_scheme_application.*',    
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
                                'far_scheme_app_details.pump_latitude',
                                'far_scheme_app_details.pump_longitude',
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
                                'far_scheme_app_details.sch_man_photo',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'far_scheme_app_details.command_area_hector',
                                'master_scheme_types.scheme_type_name',
                                'master_scheme_types.scheme_type_name_bn'
                            );
                        
        $list = $query->orderBy('id', 'desc')
                ->whereNotIn('far_scheme_application.status', [99])
                ->where('far_scheme_application.farmer_id',$user_id)->paginate(10);       

        return response([
            'success' => true,
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }

    /**
     * Farmer Scheme Application & Farmer Scheme Application details  store 
     */
    public function store(Request $request)
    {   return $request;
        $validationResult = FarmerSchemeApplicationValidation:: validate($request); 

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $applicationIdGet = DB::table('far_scheme_application')
                                ->select('application_id')
                                ->orderBy('application_id','desc')
                                ->first();

        if($applicationIdGet){
            $application_id = $applicationIdGet->application_id;        
            if( $application_id !="" ){
                $application_id+= 1;
            }
        } else {
            $application_id = 100000;
        }

        DB::beginTransaction();

        try {

            $FarmerSchemeApplication                       = new FarmerSchemeApplication();
            $FarmerSchemeApplication->farmer_id            = (int)user_id();
            $FarmerSchemeApplication->application_id       = (int)$application_id;
            $FarmerSchemeApplication->application_type_id  = isset($request->application_type_id) ? $request->application_type_id : 1;
            $FarmerSchemeApplication->email                = username();
            $FarmerSchemeApplication->scheme_type_id       = (int)$request->scheme_type_id;
            $FarmerSchemeApplication->sub_scheme_type_id       = (int)$request->sub_scheme_type_id;
            $FarmerSchemeApplication->pump_capacity_id       = (int)$request->pump_capacity_id;
            $FarmerSchemeApplication->org_id               = (int)$request->org_id;
            $FarmerSchemeApplication->name                 = $request->name;
            $FarmerSchemeApplication->name_bn              = $request->name_bn;            
            $FarmerSchemeApplication->father_name          = $request->father_name;
            $FarmerSchemeApplication->father_name_bn       = $request->father_name_bn;
            $FarmerSchemeApplication->mother_name          = $request->mother_name;
            $FarmerSchemeApplication->mother_name_bn       = $request->mother_name_bn;
            $FarmerSchemeApplication->nid                  = $request->nid;
            $FarmerSchemeApplication->payment_status       = isset($request->payment_status) ? $request->payment_status : 0;
            $FarmerSchemeApplication->far_division_id      = (int)$request->far_division_id;
            $FarmerSchemeApplication->far_district_id      = (int)$request->far_district_id;
            $FarmerSchemeApplication->far_upazilla_id      = (int)$request->far_upazilla_id;
            $FarmerSchemeApplication->far_union_id         = (int)$request->far_union_id;
            $FarmerSchemeApplication->office_id            = (int)$request->office_id;
            $FarmerSchemeApplication->far_village          = $request->far_village;
            $FarmerSchemeApplication->far_village_bn       = $request->far_village_bn;
            $FarmerSchemeApplication->far_mobile_no        = $request->far_mobile_no;
            $FarmerSchemeApplication->pump_id               = isset($request->application_type_id) ? $request->pump_id : Null;
            $FarmerSchemeApplication->total_farmer        = $request->total_farmer;

            if($FarmerSchemeApplication->save()){

                $scheme_manager_photo_path  = 'scheme-application/scheme-manager-photo';
                $general_minutes_file_path  = 'scheme-application/general-minutes';
                $scheme_lands_file_path     = 'scheme-application/scheme-lands';
                $scheme_map_file_path       = 'scheme-application/scheme-map';

                $sch_man_photo          =  $request->file('sch_man_photo') ? $request->file('sch_man_photo') : null;
                $general_minutes_file   =  $request->file('general_minutes') ? $request->file('general_minutes') : null;
                $scheme_lands_file      =  $request->file('scheme_lands') ? $request->file('scheme_lands') : null;
                $scheme_map_file        = $request->file('scheme_map') ? $request->file('scheme_map') : null;

                $null_value = null;
                if ($request->application_type_id == 2) {
                    $oldModel = FarmerSchemeApplicationDetails::where('scheme_application_id', $request->id)->first();  
                }

                if($sch_man_photo != null && $sch_man_photo != ""){
                    $sch_man_photo_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $null_value, 'sch_man_photo');
                }else{
                    $sch_man_photo_name = null;  
                }

                if($general_minutes_file != null && $general_minutes_file != ""){
                    $general_minutes_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $null_value, 'general_minutes');
                }else{
                    $general_minutes_name = null;  
                }

                if($scheme_lands_file != null && $scheme_lands_file != ""){
                    $scheme_lands_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $null_value, 'scheme_lands');
                }else{
                    $scheme_lands_name = null;  
                }

                if($scheme_map_file != null && $scheme_map_file != ""){
                    $scheme_map_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $null_value, 'scheme_map');
                }else{
                    $scheme_map_name = null;  
                }

                $scheme_application_id = $FarmerSchemeApplication->id;                
                $FarSchAppDetails = new FarmerSchemeApplicationDetails();

                $FarSchAppDetails->farmer_id                 = (int)user_id();
                $FarSchAppDetails->email                     = username();
                $FarSchAppDetails->scheme_application_id     = (int)$scheme_application_id;
                $FarSchAppDetails->sch_man_name              = $request->sch_man_name;
                $FarSchAppDetails->sch_man_name_bn           = $request->sch_man_name_bn;
                $FarSchAppDetails->sch_man_father_name       = $request->sch_man_father_name;
                $FarSchAppDetails->sch_man_father_name_bn    = $request->sch_man_father_name_bn;
                $FarSchAppDetails->sch_man_mother_name       = $request->sch_man_mother_name;
                $FarSchAppDetails->sch_man_mother_name_bn    = $request->sch_man_mother_name_bn;
                $FarSchAppDetails->sch_man_division_id       = (int)$request->sch_man_division_id;
                $FarSchAppDetails->sch_man_district_id       = (int)$request->sch_man_district_id;
                $FarSchAppDetails->sch_man_upazilla_id       = (int)$request->sch_man_upazilla_id;
                $FarSchAppDetails->sch_man_union_id          = (int)$request->sch_man_union_id;
                $FarSchAppDetails->sch_man_village           = $request->sch_man_village;
                $FarSchAppDetails->sch_man_village_bn        = $request->sch_man_village_bn;
                $FarSchAppDetails->sch_man_mobile_no         = $request->sch_man_mobile_no;
                $FarSchAppDetails->sch_man_nid               = $request->sch_man_nid;
                $FarSchAppDetails->pump_division_id          = (int)$request->pump_division_id;
                $FarSchAppDetails->pump_district_id          = (int)$request->pump_district_id;
                $FarSchAppDetails->pump_upazilla_id          = (int)$request->pump_upazilla_id;
                $FarSchAppDetails->pump_union_id             = (int)$request->pump_union_id;
                $FarSchAppDetails->pump_latitude             = $request->pump_latitude;
                $FarSchAppDetails->pump_longitude            = $request->pump_longitude;
                $FarSchAppDetails->pump_mauza_no             = $request->pump_mauza_no;
                $FarSchAppDetails->pump_mauza_no_bn          = $request->pump_mauza_no_bn;
                $FarSchAppDetails->pump_jl_no                = $request->pump_jl_no;
                $FarSchAppDetails->pump_jl_no_bn             = $request->pump_jl_no_bn;
                $FarSchAppDetails->pump_plot_no              = $request->pump_plot_no;
                $FarSchAppDetails->pump_plot_no_bn           = $request->pump_plot_no_bn; 
                $FarSchAppDetails->command_area_hector       = $request->command_area_hector; 
                $FarSchAppDetails->sch_man_photo             = isset($oldModel) ? $oldModel->sch_man_photo : $sch_man_photo_name;
                $FarSchAppDetails->general_minutes           = isset($oldModel) ? $oldModel->general_minutes : $general_minutes_name;
                $FarSchAppDetails->scheme_lands              = isset($oldModel) ? $oldModel->scheme_lands : $scheme_lands_name;
                $FarSchAppDetails->scheme_map                = isset($oldModel) ? $oldModel->scheme_map : $scheme_map_name;
                $FarSchAppDetails->affidavit_id             = (int)$request->affidavit_id;                

                if($sch_man_photo !=null && $sch_man_photo !=""){
                    GlobalFileUploadFunctoin::file_upload($request, $scheme_manager_photo_path, 'sch_man_photo', $sch_man_photo_name );
                }

                if($general_minutes_file !=null && $general_minutes_file !=""){
                    GlobalFileUploadFunctoin::file_upload($request, $general_minutes_file_path, 'general_minutes', $general_minutes_name );
                }

                if($scheme_lands_file !=null && $scheme_lands_file !=""){
                    GlobalFileUploadFunctoin::file_upload($request, $scheme_lands_file_path, 'scheme_lands', $scheme_lands_name);
                }

                if($scheme_map_file !=null && $scheme_map_file !=""){
                    GlobalFileUploadFunctoin::file_upload($request, $scheme_map_file_path, 'scheme_map', $scheme_map_name);
                }
                
                $FarSchAppDetails->save();

                // delete previous added documents
                DB::table('far_scheme_docs')
                        ->where('user_id', $request->farmer_id)
                        ->where('scheme_application_id', '=', null)
                        ->whereDate('created_at', '<', date('Y-m-d'))
                        ->delete();

                $documents = DB::table('far_scheme_docs')
                                ->where('user_id', $request->farmer_id)
                                ->where('scheme_application_id', '=', null)
                                ->whereDate('created_at', '=', date('Y-m-d'))
                                ->get();

                if (count($documents) > 0) {
                    foreach ($documents as $document) {

                        $tmpDoc = FarSchemeDocs::find($document->id);
                        $tmpDoc->scheme_application_id = $FarmerSchemeApplication->id;
                        $tmpDoc->update();
                    }
                }
            }        
            
            save_log([
                'data_id' => $FarmerSchemeApplication->id,
                'table_name' => 'far_scheme_application',
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
            'message' => 'Data save successfully',
            'data'    => $FarmerSchemeApplication
        ]);
    }

    /**
     * Farmer Scheme Application update
     */
    public function update(Request $request, $id)
    {
        $validationResult = FarmerSchemeApplicationValidation:: validate($request ,$id);  
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }
        $FarmerSchemeApplication = FarmerSchemeApplication::find($id);
        if ($request->status == 4){
            $FarmerSchemeApplication                       = new FarmerSchemeApplication();
            $FarmerSchemeApplication->status               = 1;
            $FarmerSchemeApplication->farmer_id            = (int)user_id();
            $FarmerSchemeApplication->email                = username();
            $application_id = FarmerSchemeApplication::farmerApplicationNumber();
            $FarmerSchemeApplication->application_id       = (int)$application_id;
            $FarmerSchemeApplication->application_type_id  = isset($request->application_type_id) ? $request->application_type_id : 1;
        }

        if (!$FarmerSchemeApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();
        try {
            $FarmerSchemeApplication->scheme_type_id       = (int)$request->scheme_type_id;
            $FarmerSchemeApplication->sub_scheme_type_id       = (int)$request->sub_scheme_type_id;
            $FarmerSchemeApplication->pump_capacity_id       = (int)$request->pump_capacity_id;
            $FarmerSchemeApplication->org_id               = (int)$request->org_id;
            $FarmerSchemeApplication->name                 = $request->name;
            $FarmerSchemeApplication->name_bn              = $request->name_bn;            
            $FarmerSchemeApplication->father_name          = $request->father_name;
            $FarmerSchemeApplication->father_name_bn       = $request->father_name_bn;
            $FarmerSchemeApplication->mother_name          = $request->mother_name;
            $FarmerSchemeApplication->mother_name_bn       = $request->mother_name_bn;
            $FarmerSchemeApplication->nid                  = $request->nid;
            $FarmerSchemeApplication->far_division_id      = (int)$request->far_division_id;
            $FarmerSchemeApplication->far_district_id      = (int)$request->far_district_id;
            $FarmerSchemeApplication->far_upazilla_id      = (int)$request->far_upazilla_id;
            $FarmerSchemeApplication->far_union_id         = (int)$request->far_union_id;
            $FarmerSchemeApplication->office_id            = (int)$request->office_id;
            $FarmerSchemeApplication->far_village          = $request->far_village;
            $FarmerSchemeApplication->far_village_bn       = $request->far_village_bn;
            $FarmerSchemeApplication->far_mobile_no        = $request->far_mobile_no;
            $FarmerSchemeApplication->total_farmer        = $request->total_farmer;

            if( $FarmerSchemeApplication->save() ){
                $FarSchAppDetails = FarmerSchemeApplicationDetails::where('scheme_application_id', $id)->first();
                if ($request->status == 4){
                    $extSchemeApplication = FarmerSchemeApplication::find($id);
                    $extSchemeApplication->status = 13;
                    $extSchemeApplication->save();
                    
                    $FarSchAppDetails = new FarmerSchemeApplicationDetails();
                    $FarSchAppDetails->farmer_id     = (int)user_id();
                    $FarSchAppDetails->email                = username();
                    $FarSchAppDetails->scheme_application_id     = $FarmerSchemeApplication->id;
                }

                $scheme_manager_photo_path  = 'scheme-application/scheme-manager-photo';
                $general_minutes_file_path = 'scheme-application/general-minutes';
                $scheme_lands_file_path = 'scheme-application/scheme-lands';
                $scheme_map_file_path = 'scheme-application/scheme-map';

                $sch_man_photo          =  $request->file('sch_man_photo');
                $general_minutes_file   =  $request->file('general_minutes');
                $scheme_lands_file      =  $request->file('scheme_lands');
                $scheme_map_file        = $request->file('scheme_map');

                $old_sch_man_photo      = $FarSchAppDetails->sch_man_photo;
                $old_general_minutes    = $FarSchAppDetails->general_minutes;
                $old_scheme_lands       = $FarSchAppDetails->scheme_lands;
                $old_scheme_map         = $FarSchAppDetails->scheme_map; 

                $FarSchAppDetails->sch_man_name              = $request->sch_man_name;
                $FarSchAppDetails->sch_man_name_bn           = $request->sch_man_name_bn;
                $FarSchAppDetails->sch_man_father_name       = $request->sch_man_father_name;
                $FarSchAppDetails->sch_man_father_name_bn    = $request->sch_man_father_name_bn;
                $FarSchAppDetails->sch_man_mother_name       = $request->sch_man_mother_name;
                $FarSchAppDetails->sch_man_mother_name_bn    = $request->sch_man_mother_name_bn;
                $FarSchAppDetails->sch_man_division_id       = (int)$request->sch_man_division_id;
                $FarSchAppDetails->sch_man_district_id       = (int)$request->sch_man_district_id;
                $FarSchAppDetails->sch_man_upazilla_id       = (int)$request->sch_man_upazilla_id;
                $FarSchAppDetails->sch_man_union_id          = (int)$request->sch_man_union_id;
                $FarSchAppDetails->sch_man_village           = $request->sch_man_village;
                $FarSchAppDetails->sch_man_village_bn        = $request->sch_man_village_bn;
                $FarSchAppDetails->sch_man_mobile_no         = $request->sch_man_mobile_no;
                $FarSchAppDetails->sch_man_nid               = $request->sch_man_nid;
                $FarSchAppDetails->pump_division_id          = (int)$request->pump_division_id;
                $FarSchAppDetails->pump_district_id          = (int)$request->pump_district_id;
                $FarSchAppDetails->pump_upazilla_id          = (int)$request->pump_upazilla_id;
                $FarSchAppDetails->pump_union_id             = (int)$request->pump_union_id;
                $FarSchAppDetails->pump_latitude             = $request->pump_latitude;
                $FarSchAppDetails->pump_longitude            = $request->pump_longitude;
                $FarSchAppDetails->pump_mauza_no             = $request->pump_mauza_no;
                $FarSchAppDetails->pump_mauza_no_bn          = $request->pump_mauza_no_bn;
                $FarSchAppDetails->pump_jl_no                = $request->pump_jl_no;
                $FarSchAppDetails->pump_jl_no_bn             = $request->pump_jl_no_bn;
                $FarSchAppDetails->pump_plot_no              = $request->pump_plot_no;
                $FarSchAppDetails->pump_plot_no_bn           = $request->pump_plot_no_bn;
                $FarSchAppDetails->command_area_hector       = $request->command_area_hector; 
                $FarSchAppDetails->affidavit_id              = (int)$request->affidavit_id;
               
                if($sch_man_photo != null && $sch_man_photo !=""){
                    $sch_man_photo_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $scheme_manager_photo_path,'sch_man_photo');
                    $FarSchAppDetails->sch_man_photo    = $sch_man_photo_name;
                }
               
                if($general_minutes_file != null && $general_minutes_file !=""){
                    $general_minutes_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $general_minutes_file_path,'general_minutes');
                    $FarSchAppDetails->general_minutes  = $general_minutes_name;
                }

                if($scheme_lands_file != null && $scheme_lands_file !=""){
                    $scheme_lands_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $scheme_lands_file_path,'scheme_lands');
                    $FarSchAppDetails->scheme_lands     = $scheme_lands_name;
                }

                if($scheme_map_file != null && $scheme_map_file !=""){
                    $scheme_map_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $scheme_map_file_path,'scheme_map');
                    $FarSchAppDetails->scheme_map   = $scheme_map_name;
                }

                if($FarSchAppDetails->save()){
                    
                    if($sch_man_photo !=null && $sch_man_photo !=""){                        
                        GlobalFileUploadFunctoin::file_upload($request, $scheme_manager_photo_path, 'sch_man_photo', $sch_man_photo_name, $old_sch_man_photo);
                    }

                    if($general_minutes_file !=null && $general_minutes_file !=""){                        
                        GlobalFileUploadFunctoin::file_upload($request, $general_minutes_file_path, 'general_minutes', $general_minutes_name,$old_general_minutes);
                    }

                    if($scheme_lands_file !=null && $scheme_lands_file !=""){                        
                        GlobalFileUploadFunctoin::file_upload($request, $scheme_lands_file_path, 'scheme_lands', $scheme_lands_name,$old_scheme_lands);
                    }

                    if($scheme_map_file !=null && $scheme_map_file !=""){                        
                        GlobalFileUploadFunctoin::file_upload($request, $scheme_map_file_path, 'scheme_map', $scheme_map_name,$old_scheme_map);
                    }
                }
            }  

            save_log([
                'data_id' => $FarmerSchemeApplication->id,
                'table_name' => 'far_scheme_application',
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
            'data'    => $FarmerSchemeApplication
        ]);
        
    }

    public function pendingPayment (Request $request) {
        $validationResult = PaymentValidation::validate($request);    
        if (!$validationResult['success']) {
            return response($validationResult);
        }
        $irrigation_paymnet = IrrigationPayment::find($request->id);
        $transaction_no     = strtoupper(uniqid());

        if ($irrigation_paymnet) {
            $irrigation_paymnet->transaction_no   = $transaction_no;
            $irrigation_paymnet->status   = 1;
            $irrigation_paymnet->save();
        }

        $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();

        $pay_info['s_uri']          = config('app.base_url.project_url').'scheme-application/success';
        $pay_info['f_uri']          = config('app.base_url.project_url').'scheme-application/decline';
        $pay_info['c_uri']          = config('app.base_url.project_url').'scheme-application/cancel';
        $pay_info['cust_id']        = (int)user_id();
        $pay_info['cust_name']      = $basic_info->name;
        $pay_info['cust_mobo_no']   = $basic_info->mobile_no;
        $pay_info['cust_email']     = $basic_info->email;
        $pay_info['cust_mail_addr'] = $basic_info->far_village;

        $pay_info['trnx_id']        = $transaction_no;
        $pay_info['trnx_amt']       = $irrigation_paymnet->amount;
        $pay_info['trnx_currency']  = 'BDT';
        $pay_info['ord_id']         = $irrigation_paymnet->id;
        $pay_info['ord_det']        = date('Y-m-d');

        $ekpay_payment = new EkpayLibrary();
        return $ekpay_payment->ekpay_payment($pay_info);

    }

    /**
     * Farmer Scheme Application status update
     */
    public function toggleStatus($id)
    {
        $FarmerSchemeApplication = FarmerSchemeApplication::find($id);

        if (!$FarmerSchemeApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $FarmerSchemeApplication->status = $FarmerSchemeApplication->status ? 1 : 2;
        $FarmerSchemeApplication->update();

        save_log([
            'data_id' => $FarmerSchemeApplication->id,
            'table_name' => 'far_scheme_application',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Not Farmer Scheme Application toggle status updated successfully',
            'data'    => $FarmerSchemeApplication
        ]);
    }

    /**
     * Farmer Scheme Application destroy
     */
    public function destroy($id)
    {
        $FarmerSchemeApplication = FarmerSchemeApplication::find($id);
        $FarSchAppDetails = FarmerSchemeApplicationDetails::where('scheme_application_id', $id);

            
        if (!$FarmerSchemeApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }
        if( $FarSchAppDetails->delete() ){
            $FarmerSchemeApplication->delete();
        }

        save_log([
            'data_id' => $id,
            'table_name' => 'far_scheme_application & far_scheme_app_details',
            'execution_type' => 2
        ]);

        return response([   
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

    /*
     * Farmer Scheme Application Status As Processing
    */
    public function updateAppStatusAsProcessing($id)
    {
        $FarmerSchemeApplication = FarmerSchemeApplication::find($id);

        if (!$FarmerSchemeApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        if($FarmerSchemeApplication->status !=2) {
            $FarmerSchemeApplication->status = 2;
            $FarmerSchemeApplication->update();
        } else {
            return response([
                'success' => false,
                'message' => 'Farmer scheme application status already processing!!'
            ]);
        }

        save_log([
            'data_id' => $FarmerSchemeApplication->id,
            'table_name' => 'far_scheme_application',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Not Farmer Scheme Application status updated successfully',
            'data'    => $FarmerSchemeApplication
        ]);
    }

    /*
     * Farmer Scheme Application Status As Approved
    */
    public function updateAppStatusAsApproved($id)
    {
        $farmerSchemeApplication = FarmerSchemeApplication::find($id);

        if (!$farmerSchemeApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        if($farmerSchemeApplication->status !=3) {
            $farmerSchemeApplication->status = 3;
            $farmerSchemeApplication->update();
        } else {
            return response([
                'success' => false,
                'message' => 'Farmer scheme application status already approved!!'
            ]);
        }

        save_log([
            'data_id' => $farmerSchemeApplication->id,
            'table_name' => 'far_scheme_application',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Farmer scheme application status updated as approved successfully',
            'data'    => $farmerSchemeApplication
        ]);
    }

    /*
     * Farmer Scheme Application Status As Reject
    */
    public function updateAppStatusAsReject($id)
    {
        $farmerSchemeApplication = FarmerSchemeApplication::find($id);

        if (!$farmerSchemeApplication) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

         if($farmerSchemeApplication->status !=99) {
            $farmerSchemeApplication->status = 99;
            $farmerSchemeApplication->update();
        } else {
            return response([
                'success' => false,
                'message' => 'Farmer scheme application status already rejected!!'
            ]);
        }

        save_log([
            'data_id' => $farmerSchemeApplication->id,
            'table_name' => 'far_scheme_application',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Farmer scheme application status updated as reject successfully',
            'data'    => $farmerSchemeApplication
        ]);
    }

    /*Scheme application documents store*/
    public function documentStore(Request $request) 
    {
        $file_path 	= 'scheme-application-document';

        try {
            $attachment_name = null;

            $document = new FarSchemeDocs();
            $document->user_id              = $request->user_id;
            $document->document_title       = $request->document_title;
            $document->document_title_bn    = $request->document_title_bn;

            if($request->attachment && $request->attachment != ""){

                $file = $request->attachment;
                $rules = ['attachment' => 'required|mimes:png,gif,jpeg,svg,tiff,pdf,doc,docx,tex,txt,rtf'];
                $validator = Validator::make(['attachment' => $file], $rules);

                $attachment_name = time().'.' . $file->getClientOriginalExtension();

                if ($validator->fails()) {
                    return ([
                        'success' => false,
                        'errors' => $validator->errors()
                    ]);
                }
            }

            $document->attachment   =  $attachment_name ?? null;

            if($document->save()){
                $fileDestinationPath = storage_path("uploads/{$file_path}/original");

                if ($file != null && $file != "") {
                    GlobalFileUploadFunctoin::is_dir_set_permission($fileDestinationPath);
                    $file->move($fileDestinationPath, $attachment_name);
                }
            }

            save_log([
                'data_id'   => $document->id,
                'table_name'=> 'far_scheme_docs'
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
            'data'    => $document
        ]);
    }

    /**
     * get documents
    */
    public function getDocument($id) 
    {   
        $query = FarSchemeDocs::where('scheme_application_id', $id)->get();

        if ($query->count() > 0) {
            return response([
                'success' => true,
                'message' => 'Application document list',
                'data'    => $query
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Data not found',
                'data'    => []
            ]);
        }
    }

    /**
     * destroy document
     */
    public function destroyDocument ($id)
    {
        $document = FarSchemeDocs::find($id);

        if (!$document) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $file = $document->attachment;
        $fileDestinationPath = storage_path('uploads/scheme-application-document/original');

        if(file_exists($fileDestinationPath.'/'. $file)){
            unlink($fileDestinationPath.'/'.$file);
        }

        $document->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_scheme_docs',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
