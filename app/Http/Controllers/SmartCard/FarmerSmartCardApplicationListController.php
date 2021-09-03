<?php

namespace App\Http\Controllers\SmartCard;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\SmartCard\FarmerSmartCardReview;
use App\Models\SmartCard\FarmerSmartCardRejects;
use App\Models\SmartCard\FarmerSmartCardApplication;

class FarmerSmartCardApplicationListController extends Controller
{	
	 public function __construct()
    {
      // 
    }

        
    public function singleDetails($id){
        return FarmerSmartCardApplication::find($id)->first();
    }

    /*get all Smart Card New Application */
    public function indexNewApp(Request $request)
    {

        $query = FarmerSmartCardApplication::select('*')->where('payment_status', 1);

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->application_id) {
            $query = $query->where('application_id', $request->application_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->name) {
            $query = $query->where('name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->father_name) {
            $query = $query->where('father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->spouse_name) {
            $query = $query->where('spouse_name', 'like', "{$request->spouse_name}%")
                            ->orWhere('spouse_name_bn', 'like', "{$request->spouse_name}%");
        }

        if ($request->nid) {
            $query = $query->where('nid', $request->nid);
        }

        if ($request->mobile_no) {
            $query = $query->where('mobile_no', $request->mobile_no);
        }

        if ($request->gender) {
            $query = $query->where('gender', $request->gender);
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
            $query = $query->where('far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->ward_no) {
            $query = $query->where('ward_no', $request->ward_no);
        }

        if ($request->date_of_birth) {
            $query = $query->where('date_of_birth', $request->date_of_birth);
        }

        if ($request->qualification) {
            $query = $query->where('qualification', $request->qualification);
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }
        $query = $query->where('reissue_status', 1);

        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Smart card application list',
            'data' => $list
        ]);
    }

    /*get all Smart Card Reissue Application */
    public function indexReissueApp(Request $request)
    {
       $query = DB::table('far_smart_card_apps')                          
                    ->leftJoin('far_smart_card_review','far_smart_card_apps.id', '=','far_smart_card_review.far_smart_card_apps_id')
                    ->select('far_smart_card_apps.*',
                    		'far_smart_card_review.note as review_note',
                    		'far_smart_card_review.note_bn as review_note_bn',
                            'far_smart_card_review.created_at as review_created_at')
                    ->where('far_smart_card_apps.reissue_status', 2)
                    ->where('far_smart_card_apps.payment_status', 1);

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }

        if ($request->application_id) {
            $query = $query->where('application_id', $request->application_id);
        }

        if ($request->email) {
            $query = $query->where('email', $request->email);
        }

        if ($request->name) {
            $query = $query->where('name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->father_name) {
            $query = $query->where('father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        }

        if ($request->mother_name) {
            $query = $query->where('mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->spouse_name) {
            $query = $query->where('spouse_name', 'like', "{$request->spouse_name}%")
                            ->orWhere('spouse_name_bn', 'like', "{$request->spouse_name}%");
        }

        if ($request->nid) {
            $query = $query->where('nid', $request->nid);
        }

        if ($request->mobile_no) {
            $query = $query->where('mobile_no', $request->mobile_no);
        }

        if ($request->gender) {
            $query = $query->where('gender', $request->gender);
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
            $query = $query->where('far_village', 'like', "{$request->far_village}%")
                            ->orWhere('far_village_bn', 'like', "{$request->far_village}%");
        }

        if ($request->ward_no) {
            $query = $query->where('ward_no', $request->ward_no);
        }

        if ($request->date_of_birth) {
            $query = $query->where('date_of_birth', $request->date_of_birth);
        }

        if ($request->qualification) {
            $query = $query->where('qualification', $request->qualification);
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        // $query = $query->where('reissue_status', 2);

        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Smart card reissue application list',
            'data' => $list
        ]);
    }

    /*get all Smart Card Review Application */
    public function indexReviewApp(Request $request)
    {
	  	$query = DB::table('far_smart_card_apps')                          
                    ->leftJoin('far_smart_card_review','far_smart_card_apps.id', '=','far_smart_card_review.far_smart_card_apps_id')
                    ->select('far_smart_card_apps.*',
                    		'far_smart_card_review.note as review_note',
                    		'far_smart_card_review.note_bn as review_note_bn',
                    		'far_smart_card_review.created_at as review_created_at');
                    
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

        if ($request->to_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        $query = $query->where('far_smart_card_apps.status', 7);
     
        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Smart card application review list',
            'data' => $list
        ]);
    }

    /*get all Smart Card Rejects Application */
    public function indexRejectsApp(Request $request)
    {   
        //$query = FarmerSmartCardApplication::with(['smartCardReview','smartCardRejects']);
	  	$query = DB::table('far_smart_card_apps')                          
                    ->leftJoin('far_smart_card_review','far_smart_card_apps.id', '=','far_smart_card_review.far_smart_card_apps_id') 
                    ->leftJoin('far_smart_card_rejects','far_smart_card_apps.id', '=','far_smart_card_rejects.far_smart_card_apps_id')
                    ->select('far_smart_card_apps.*',
                    		'far_smart_card_review.note as review_note',
                    		'far_smart_card_review.note_bn as review_note_bn',
                    		'far_smart_card_review.created_at as review_created_at',
                    		'far_smart_card_rejects.reject_note',
                    		'far_smart_card_rejects.reject_note_bn',
                    		'far_smart_card_rejects.created_at as reject_created_at');
                    
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

        if ($request->reissue_status) {
            $query = $query->where('far_smart_card_apps.reissue_status', $request->reissue_status);
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        $query = $query->where('far_smart_card_apps.status', 6);
     
        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Smart card application rejected list',
            'data' => $list
        ]);
    }   

   
    /*get all Smart Card approval List*/
    public function approvalList(Request $request)
    {
        //$query = FarmerSmartCardApplication::with('smartCardReview');
        $query = DB::table('far_smart_card_apps')                          
                    ->leftJoin('far_smart_card_review','far_smart_card_apps.id', '=','far_smart_card_review.far_smart_card_apps_id')
                    ->select('far_smart_card_apps.*',
                            'far_smart_card_review.note as review_note',
                            'far_smart_card_review.note_bn as review_note_bn',
                            'far_smart_card_review.created_at as review_created_at');                           
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

        if ($request->reissue_status) {
            $query = $query->where('far_smart_card_apps.reissue_status', $request->reissue_status);
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        $query = $query->where('far_smart_card_apps.status', 3);
     
        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Smart card application rejected list',
            'data' => $list
        ]);
    } 

     /*get all Smart Card Generated List*/
    public function cardGeneratedList(Request $request)
    {
        //$query = FarmerSmartCardApplication::with('smartCardReview');
        $query = DB::table('far_smart_card_apps')                          
                    ->leftJoin('far_smart_card_review','far_smart_card_apps.id', '=','far_smart_card_review.far_smart_card_apps_id')
                    ->select('far_smart_card_apps.*',
                            'far_smart_card_review.note as review_note',
                            'far_smart_card_review.note_bn as review_note_bn',
                            'far_smart_card_review.created_at as review_created_at');                           
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

        if ($request->reissue_status) {
            $query = $query->where('far_smart_card_apps.reissue_status', $request->reissue_status);
        }

        if ($request->to_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }

        if ($request->from_date) {
            $query = $query->whereDate('far_smart_card_apps.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }
        $query = $query->where('far_smart_card_apps.status', 4);
     
        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Smart card generated list',
            'data' => $list
        ]);
    } 


     
}
