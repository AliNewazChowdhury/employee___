<?php
namespace App\Http\Controllers\FarmerProfile;

use App\Http\Controllers\Controller;
use App\Http\Validations\FarmerProfile\FarmerBasicInfosValidations;
use App\Models\FarmerProfile\FarmerBasicInfos;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;

use DB;

class FarmerBasicInfosController extends Controller
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
    * get all Farmer Basic Infos
    */
    public function index(Request $request)
    {
        $query = FarmerBasicInfos::select('*');


        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->name) {
            $query = $query->where('name', 'like', "%{$request->name}%")
                        ->orWhere('name_bn', 'like', "%{$request->name}%");
        }

        if ($request->father_name) {
            $query = $query->where('father_name', 'like', "%{$request->father_name}%")
                        ->orWhere('father_name_bn', 'like', "%{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('mother_name', 'like', "%{$request->mother_name}%")
                        ->orWhere('mother_name_bn', 'like', "%{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('nid', $request->nid);
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

        if ($request->far_union_id) {
            $query = $query->where('far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_village', 'like', "%{$request->far_village}%")
                        ->orWhere('far_village_bn', 'like', "%{$request->far_village}%");
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Farmer basic infos list',
            'data' => $list
        ]);
    }

    /**
    * get Loged in Farmer Basic Infos
    */
    public function singleIndex(Request $request)
    {
        $userId = user_id();
        $query  = FarmerBasicInfos::select('*');
        $query  = $query->where('farmer_id', $userId);
        $list   = $query->get();

        return response([
            'success' => true,
            'message' => 'Farmer basic infos',
            'data' => $list
        ]);
    }

    /**
    * Farmer Basic Infos store
    */
    public function store(Request $request)
    {   
        $validationResult = FarmerBasicInfosValidations:: validate($request);
        $profile_path   = 'farmer-basic';
        $nid_path       = 'farmer-basic-nid';
        $profile_logo   =  $request->file('attachment');
        $nid_photo      =  $request->file('nid_photo');
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }
        
        if($profile_logo != null && $profile_logo != ""){
            $profile_logoName = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $profile_path,'attachment');
        }
        
        if($nid_photo != null && $nid_photo != ""){
            $nid_photoName = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $nid_path,'nid_photo');
        }

        try {
            $farBasicInfos                  = new FarmerBasicInfos();
            $farBasicInfos->farmer_id       = (int)$request->farmer_id??user_id();
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
            $farBasicInfos->far_division_id = (int)$request->far_division_id;
            $farBasicInfos->far_district_id = (int)$request->far_district_id;
            $farBasicInfos->far_upazilla_id = (int)$request->far_upazilla_id;
            $farBasicInfos->far_union_id    = (int)$request->far_union_id;
            $farBasicInfos->far_village     = $request->far_village;
            $farBasicInfos->far_village_bn  = $request->far_village_bn;
            $farBasicInfos->status          = ($request->status == 2) ? 2 : 1;

            $farBasicInfos->attachment      = $profile_logoName ?? null;
            $farBasicInfos->nid_photo       = $nid_photoName ?? null;

            if($farBasicInfos->save()){

                if($profile_logo !=null && $profile_logo !=""){
                    GlobalFileUploadFunctoin::file_upload($request, $profile_path, 'attachment', $profile_logoName);
                }

                if($nid_photo !=null && $nid_photo !=""){
                    GlobalFileUploadFunctoin::file_upload($request, $nid_path, 'nid_photo', $nid_photoName);
                }

            }
            save_log([
                'data_id'    => $farBasicInfos->id,
                'table_name' => 'far_basic_infos'
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
            'data'    => $farBasicInfos
        ]);
    }

    /**
     * Farmer Basic Infos update
    */
    public function update(Request $request, $id)
    {   
        $validationResult = FarmerBasicInfosValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $profile_path   = 'farmer-basic';
        $nid_path       = 'farmer-basic-nid';
        $profile_logo   =  $request->file('attachment');
        $nid_photo      =  $request->file('nid_photo');
        
        $farmerID = (int)$request->farmer_id ?? user_id();
        $farBasicInfos = FarmerBasicInfos::where('farmer_id', $farmerID)->firstOrNew();
        $old_attachment = $farBasicInfos->attachment;
        $old_nid_photo = $farBasicInfos->nid_photo;

        if($profile_logo != null && $profile_logo != ""){
            $profile_logoName = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $profile_path,'attachment');
        }

        if($nid_photo != null && $nid_photo != ""){
            $nid_photoName = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $nid_path,'nid_photo');
        }

        try {

            $farBasicInfos->farmer_id       = (int)$request->farmer_id??user_id();
            $farBasicInfos->email           = $request->email?? username();;
            $farBasicInfos->name            = $request->name;
            $farBasicInfos->name_bn         = $request->name_bn;
            $farBasicInfos->gender          = $request->gender;
            $farBasicInfos->mobile_no       = $request->mobile_no;
            $farBasicInfos->father_name     = $request->father_name;
            $farBasicInfos->father_name_bn  = $request->father_name_bn;
            $farBasicInfos->mother_name     = $request->mother_name;
            $farBasicInfos->mother_name_bn  = $request->mother_name_bn;
            $farBasicInfos->nid             = $request->nid;
            $farBasicInfos->far_division_id = (int)$request->far_division_id;
            $farBasicInfos->far_district_id = (int)$request->far_district_id;
            $farBasicInfos->far_upazilla_id = (int)$request->far_upazilla_id;
            $farBasicInfos->far_union_id    = (int)$request->far_union_id;
            $farBasicInfos->far_village     = $request->far_village;
            $farBasicInfos->far_village_bn  = $request->far_village_bn;
            $farBasicInfos->status          = ($request->status == 2) ? 2 : 1;

            $farBasicInfos->attachment      = $profile_logoName ?? $old_attachment;
            $farBasicInfos->nid_photo       = $nid_photoName ?? $old_nid_photo;
            
            if($farBasicInfos->save()){
                if($profile_logo !=null && $profile_logo !=""){
                    GlobalFileUploadFunctoin::file_upload($request, $profile_path, 'attachment', $profile_logoName, $old_attachment);
                }
                if($nid_photo != null && $nid_photo != ""){
                    GlobalFileUploadFunctoin::file_upload($request, $nid_path, 'nid_photo', $nid_photoName, $old_nid_photo);
                }
            }            

            save_log([
                'data_id'       => $farBasicInfos->id,
                'table_name'    => 'far_basic_infos',
                'execution_type'=> 1
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
            'data'    => $farBasicInfos
        ]);
    }

    /**
     * Check farmer account complete or not
    */
    public function checkUser(Request $request)
    {
        $id = user_id();

        $farBasicInfos = FarmerBasicInfos::where('farmer_id', $id)
            ->where('status', 2)
            ->first();

        if (!$farBasicInfos) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $response = [
            'success' => true,
            'data'  => $farBasicInfos,
            'message' => 'Farmer profile complete'
        ];

        if (!$request->filled('with_data') && !$request->with_data) {
            $data = DB::table('far_basic_infos')
                ->leftJoin('far_smart_card_apps','far_smart_card_apps.farmer_id', '=','far_basic_infos.farmer_id')
                ->select(
                    'far_basic_infos.*',
                    'far_smart_card_apps.id as smart_card_id'
                )
                ->where('far_basic_infos.farmer_id', $id)
                ->first();
            $response['data'] = $data;
        }

        return response($response);
    }
    public function changeMobile(Request $request)
    {
        $id = user_id();
        $farBasicInfos = FarmerBasicInfos::where('farmer_id', $id)->first();
        if ($farBasicInfos) {
            $farBasicInfos->mobile_no = $request->mobile_no;
            $farBasicInfos->save();
    
            if (!$farBasicInfos) {
                return response([
                    'success' => false,
                    'message' => 'Data not found.'
                ]);
            }
            $response = [
                'success' => true,
                'data'  => $farBasicInfos,
                'message' => 'Mobile number save success'
            ];
            return response($response);
        } 
        return true;
    }

    protected function saveOrUpdate($data)
    {
        # code...
    }
}
