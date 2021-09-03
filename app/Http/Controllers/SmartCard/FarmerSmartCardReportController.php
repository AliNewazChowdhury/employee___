<?php
namespace App\Http\Controllers\SmartCard;

use App\Http\Controllers\Controller;
use App\Models\SmartCard\FarmerSmartCardApplication;
use App\Models\SmartCard\FarmerSmartCardReview;
use App\Models\SmartCard\FarmerSmartCardRejects;
use Illuminate\Http\Request;
use DB;

class FarmerSmartCardReportController extends Controller
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
    * get all Smart Card Report Application
    */
    public function indexReport(Request $request)
    {
	  	$query = FarmerSmartCardApplication::with(['smartCardReview','smartCardRejects']);  
                    
        if ($request->org_id) {
            $query = $query->where('far_smart_card_apps.org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('far_smart_card_apps.farmer_id', $request->farmer_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_smart_card_apps.application_id', $request->application_id);
        }

        if ($request->email) {
            $query = $query->where('far_smart_card_apps.email', $request->email);
        }

        if ($request->name) {
            $query = $query->where('far_smart_card_apps.name', 'like', "{$request->name}%")
                            ->orWhere('far_smart_card_apps.name_bn', 'like', "{$request->name}%");
        }

        if ($request->father_name) {
            $query = $query->where('far_smart_card_apps.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('far_smart_card_apps.father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('far_smart_card_apps.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('far_smart_card_apps.mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->spouse_name) {
            $query = $query->where('far_smart_card_apps.spouse_name', 'like', "{$request->spouse_name}%")
                            ->orWhere('far_smart_card_apps.spouse_name_bn', 'like', "{$request->spouse_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_smart_card_apps.nid', $request->nid);
        }

        if ($request->mobile_no) {
            $query = $query->where('far_smart_card_apps.mobile_no', $request->mobile_no);
        }

        if ($request->gender) {
            $query = $query->where('far_smart_card_apps.gender', $request->gender);
        }

        if ($request->far_division_id) {
            $query = $query->where('far_smart_card_apps.far_division_id', $request->far_division_id);
        }

        if ($request->far_district_id) {
            $query = $query->where('far_smart_card_apps.far_district_id', $request->far_district_id);
        }

        if ($request->far_upazilla_id) {
            $query = $query->where('far_smart_card_apps.far_upazilla_id', $request->far_upazilla_id);
        }

        if ($request->far_union_id) {
            $query = $query->where('far_smart_card_apps.far_union_id', $request->far_union_id);
        }

        if ($request->far_village) {
            $query = $query->where('far_smart_card_apps.far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_smart_card_apps.far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->ward_no) {
            $query = $query->where('far_smart_card_apps.ward_no', $request->ward_no);
        }

        if ($request->date_of_birth) {
            $query = $query->where('far_smart_card_apps.date_of_birth', $request->date_of_birth);
        }

        if ($request->qualification) {
            $query = $query->where('far_smart_card_apps.qualification', $request->qualification);
        }

        if ($request->status) {
            $query = $query->where('far_smart_card_apps.status', $request->status);
        }

        if ($request->reissue_status) {
            $query = $query->where('far_smart_card_apps.reissue_status', $request->reissue_status);
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '<=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '>=', date('Y-m-d', strtotime($request->to_date)));
        }        
        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Smart card application report list',
            'data' => $list
        ]);
    }
}
