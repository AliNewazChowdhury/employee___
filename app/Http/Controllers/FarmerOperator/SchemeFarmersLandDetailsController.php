<?php

namespace App\Http\Controllers\FarmerOperator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\FarmerOperator\FarmerLandDetailsValidations;
use App\Models\FarmerOperator\FarmerLandDetails;
use Illuminate\Support\Facades\DB;

class SchemeFarmersLandDetailsController extends Controller
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
     * get all Scheme Farmers Land Details
     */
    public function index(Request $request, $id)
    {
        $query = DB::table('farmer_land_details')
                     ->where('application_id', $id)
                     ->where('application_type', $request->application_type);

        if ($request->far_name) {
            $query = $query->where('far_name', 'like', "%{$request->far_name}%")
                           ->orWhere('far_name_bn', 'like', "%{$request->far_name}%");
        }

        if ($request->far_father_name) {
            $query = $query->where('far_father_name', 'like', "%{$request->far_father_name}%")
                           ->orWhere('far_father_name_bn', 'like', "%{$request->far_father_name}%");
        }

         if ($request->far_mother_name) {
            $query = $query->where('far_mother_name', 'like', "%{$request->far_mother_name}%")
                           ->orWhere('far_mother_name_bn', 'like', "%{$request->far_mother_name}%");
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

        if ($request->far_mobile_no) {
            $query = $query->where('far_mobile_no', $request->far_mobile_no);
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

//        $list = $query->paginate($request->per_page??10);
        $list = $query->get();

        return response([
            'success' => true,
            'message' => "Scheme farmer's land details list",
            'data' => $list
        ]);
    }

    /**
     * Scheme Farmers Land Details store
     */
    public function store(Request $request)
    {
        $validationResult = FarmerLandDetailsValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();
        try {
            $schFarLandDetls                        = new FarmerLandDetails();
            $schFarLandDetls->application_id        = (int)$request->application_id;
            $schFarLandDetls->application_type      = $request->application_type;
            $schFarLandDetls->far_name              = $request->far_name;
            $schFarLandDetls->far_name_bn           = $request->far_name_bn;
            $schFarLandDetls->far_father_name       = $request->far_father_name;
            $schFarLandDetls->far_father_name_bn    = $request->far_father_name_bn;
            $schFarLandDetls->far_mother_name       = $request->far_mother_name;
            $schFarLandDetls->far_mother_name_bn    = $request->far_mother_name_bn;
            $schFarLandDetls->far_division_id       = (int)$request->far_division_id;
            $schFarLandDetls->far_district_id       = (int)$request->far_district_id;
            $schFarLandDetls->far_upazilla_id       = (int)$request->far_upazilla_id;
            $schFarLandDetls->far_union_id          = (int)$request->far_union_id;
            $schFarLandDetls->far_village           = $request->far_village;
            $schFarLandDetls->far_village_bn        = $request->far_village_bn;
            $schFarLandDetls->far_nid               = $request->far_nid;
            $schFarLandDetls->far_mobile_no         = $request->far_mobile_no;
            $schFarLandDetls->own_land_amount       = $request->own_land_amount;
            $schFarLandDetls->borga_land_amount     = $request->borga_land_amount;
            $schFarLandDetls->lease_land_amount     = $request->lease_land_amount;
            $schFarLandDetls->total_land_amount     = $request->total_land_amount;
            $schFarLandDetls->aus_crop_land         = $request->aus_crop_land;
            $schFarLandDetls->amon_crop_land        = $request->amon_crop_land;
            $schFarLandDetls->boro_crop_land        = $request->boro_crop_land;
            $schFarLandDetls->other_crop_land       = $request->other_crop_land;
            $schFarLandDetls->remarks               = $request->remarks;
            $schFarLandDetls->save();

            $addedFarmer = 0;
            if ($schFarLandDetls) {
                $appId      = $request->application_id;
                $table = $request->application_type == 1 ? 'far_scheme_application' : 'pump_informations';
                $schemeApps = DB::table($table)->where('id', $appId);
                $addedFarmer = (int)$schemeApps->first()->added_farmer;
                $addedFarmer = $addedFarmer + 1;
                $schemeApps->update(['added_farmer' => $addedFarmer]);
            }
            DB::commit();
            save_log([
                'data_id'    => $schFarLandDetls->id,
                'table_name' => 'farmer_land_details'
            ]);

        } catch (\Exception $ex) {
            DB::rollBack();
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $schFarLandDetls,
            'added_farmer' => $addedFarmer
        ]);
    }

    /**
     * Scheme Farmers Land Details update
     */
    public function update(Request $request, $id)
    {
        $validationResult = FarmerLandDetailsValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $schFarLandDetls = FarmerLandDetails::find($id);

        if (!$schFarLandDetls) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $schFarLandDetls->application_id        = (int)$request->application_id;
            $schFarLandDetls->application_type      = 1;
            $schFarLandDetls->far_name              = $request->far_name;
            $schFarLandDetls->far_name_bn           = $request->far_name_bn;
            $schFarLandDetls->far_father_name       = $request->far_father_name;
            $schFarLandDetls->far_father_name_bn    = $request->far_father_name_bn;
            $schFarLandDetls->far_mother_name       = $request->far_mother_name;
            $schFarLandDetls->far_mother_name_bn    = $request->far_mother_name_bn;
            $schFarLandDetls->far_division_id       = (int)$request->far_division_id;
            $schFarLandDetls->far_district_id       = (int)$request->far_district_id;
            $schFarLandDetls->far_upazilla_id       = (int)$request->far_upazilla_id;
            $schFarLandDetls->far_union_id          = (int)$request->far_union_id;
            $schFarLandDetls->far_village           = $request->far_village;
            $schFarLandDetls->far_village_bn        = $request->far_village_bn;
            $schFarLandDetls->far_nid               = $request->far_nid;
            $schFarLandDetls->far_mobile_no         = $request->far_mobile_no;
            $schFarLandDetls->own_land_amount       = $request->own_land_amount;
            $schFarLandDetls->borga_land_amount     = $request->borga_land_amount;
            $schFarLandDetls->lease_land_amount     = $request->lease_land_amount;
            $schFarLandDetls->total_land_amount     = $request->total_land_amount;
            $schFarLandDetls->aus_crop_land         = $request->aus_crop_land;
            $schFarLandDetls->amon_crop_land        = $request->amon_crop_land;
            $schFarLandDetls->boro_crop_land        = $request->boro_crop_land;
            $schFarLandDetls->other_crop_land       = $request->other_crop_land;
            $schFarLandDetls->remarks               = $request->remarks;
            $schFarLandDetls->update();

            save_log([
                'data_id'       => $schFarLandDetls->id,
                'table_name'    => 'farmer_land_details',
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
            'data'    => $schFarLandDetls
        ]);
    }

    /**
     * Scheme Farmers Land Details status update
     */
    public function toggleStatus($id)
    {
        $schFarLandDetls = FarmerLandDetails::find($id);

        if (!$schFarLandDetls) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $schFarLandDetls->status = $schFarLandDetls->status ? 1 : 2;
        $schFarLandDetls->update();

        save_log([
            'data_id'       => $schFarLandDetls->id,
            'table_name'    => 'farmer_land_details',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $schFarLandDetls
        ]);
    }

    /**
     * Scheme Farmers Land Details destroy
     */
    public function destroy($id)
    {
        $schFarLandDetls = FarmerLandDetails::find($id);

        if (!$schFarLandDetls) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $schFarLandDetls->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'farmer_land_details',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
