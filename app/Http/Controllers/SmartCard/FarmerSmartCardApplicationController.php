<?php
namespace App\Http\Controllers\SmartCard;

use App\Http\Validations\SmartCard\FarmerSmartCardApplicationValidations;
use App\Http\Controllers\Controller;
use App\Models\SmartCard\FarmerSmartCardApplication;
use App\Models\SmartCard\FarmerSmartCardReview;
use App\Models\SmartCard\FarmerSmartCardRejects;
use Illuminate\Http\Request;
use DB;
use App\Http\Validations\FarmerOperator\PaymentValidation;
use App\Library\EkpayLibrary;
use App\Models\Payment\IrrigationPayment;
use App\Models\FarmerProfile\FarmerBasicInfos;
use Validator;
use App\Helpers\GlobalFileUploadFunctoin;
class FarmerSmartCardApplicationController extends Controller
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
     * get all Smart Card Application
     */
    public function dashboard(Request $request)
    {
        $query =FarmerSmartCardApplication::latest();
        if (!empty($request->org_id)) {
            $query = $query->where('org_id', $request->org_id);
        }
        if (!empty($request->upazilla_id)) {
            $query = $query->where('far_upazilla_id', $request->upazilla_id);
        }
        $total=$query->get();
        return response([
            'success' => true,
            'message' => 'Smart card application list',
            'data' => array(
                'data'=>$request->org_id,
                'total' =>$total->count(),
                'pending' =>$total->where('status',1)->count(),
                'issued' =>$total->where('status',5)->count()
            )
        ]);
    }
    public function index(Request $request)
    {
        $query = DB::table('far_smart_card_apps')
        ->leftJoin('irrigation_payments','far_smart_card_apps.id', '=','irrigation_payments.far_application_id')
        ->leftJoin('far_smart_card_review','far_smart_card_apps.id', '=','far_smart_card_review.far_smart_card_apps_id')
        ->leftJoin('far_smart_card_rejects','far_smart_card_apps.id', '=','far_smart_card_rejects.far_smart_card_apps_id')
        ->select('far_smart_card_apps.*',
                'far_smart_card_review.note as review_note',
                'far_smart_card_review.note_bn as review_note_bn',
                'far_smart_card_review.created_at as review_created_at',
                'far_smart_card_rejects.reject_note',
                'far_smart_card_rejects.reject_note_bn',
                'far_smart_card_rejects.created_at as reject_created_at',
                'irrigation_payments.amount',
                'irrigation_payments.master_payment_id',
                'irrigation_payments.pay_status',
                'irrigation_payments.id as payment_id')
        ->where('irrigation_payments.payment_type_id', 1)
        ->where('irrigation_payments.application_type', 3)
        ->where('far_smart_card_apps.farmer_id', user_id())
        ->orderBy('created_at', 'desc');

        if ($request->org_id) {
            $query = $query->where('far_smart_card_apps.org_id', $request->org_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('far_smart_card_apps.farmer_id', $request->farmer_id);
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

        if ($request->reissue_status) {
            $query = $query->where('reissue_status', $request->reissue_status);
        }

        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Smart card application list',
            'data' => $list
        ]);
    }


    /*++++++*/
    public function singleIndex()
    {
       	$logInUsr = user_id();
        $query = FarmerSmartCardApplication::select('*');
        $query = $query->where('farmer_id', $logInUsr);
        $list  = $query->get();

        return response([
            'success' => true,
            'message' => 'Smart card application list',
            'data' => $list
        ]);
    }

    /**
     * Smart Card Application & Smart Card Application details  store
     */
    public function store(Request $request)
    {

        $validationResult = FarmerSmartCardApplicationValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path  = 'smart-card-apps';
        $attachment =  $request->file('attachment');

        $getIdSerial = DB::table('far_smart_card_apps')
            ->latest()
            ->select('id_serial')
            ->orderBy('id','desc')
            ->first();

        if ($getIdSerial) {
            $idSerial = $getIdSerial->id_serial;
            if( $idSerial !="" ){
                $idSerial+= 1;
            }
        } else {
            $idSerial = 1000;
        }

        $applicationId = "smtc#".$idSerial;

        DB::beginTransaction();
        try {

            $farSmartCardApp                     = new FarmerSmartCardApplication();
            $farSmartCardApp->org_id            = (int)$request->org_id;
            $farSmartCardApp->farmer_id         = (int)user_id();
            $farSmartCardApp->id_serial         = (int)$idSerial;
            $farSmartCardApp->application_id    = $applicationId;
            $farSmartCardApp->email             = $request->email??null;
            $farSmartCardApp->name              = $request->name??null;
            $farSmartCardApp->name_bn           = $request->name_bn??null;
            $farSmartCardApp->father_name       = $request->father_name??null;
            $farSmartCardApp->father_name_bn    = $request->father_name_bn??null;
            $farSmartCardApp->mother_name       = $request->mother_name??null;
            $farSmartCardApp->mother_name_bn    = $request->mother_name_bn??null;
            $farSmartCardApp->marital_status    = $request->marital_status??null;
            $farSmartCardApp->spouse_name       = $request->spouse_name??null;
            $farSmartCardApp->spouse_name_bn    = $request->spouse_name_bn??null;
            $farSmartCardApp->no_of_child       = $request->no_of_child??0;
            $farSmartCardApp->nid               = $request->nid??null;
            $farSmartCardApp->mobile_no         = $request->mobile_no??null;
            $farSmartCardApp->gender            = $request->gender??null;
            $farSmartCardApp->far_division_id   = (int)$request->far_division_id??null;
            $farSmartCardApp->far_district_id   = (int)$request->far_district_id??null;
            $farSmartCardApp->far_upazilla_id   = (int)$request->far_upazilla_id??null;
            $farSmartCardApp->far_union_id      = (int)$request->far_union_id??null;
            $farSmartCardApp->office_id         = (int)$request->office_id;
            $farSmartCardApp->far_village       = $request->far_village??null;
            $farSmartCardApp->far_village_bn    = $request->far_village_bn??null;
            $farSmartCardApp->ward_no           = $request->ward_no??null;
            $farSmartCardApp->date_of_birth     = $request->date_of_birth??null;
            $farSmartCardApp->qualification     = $request->qualification??null;
            $farSmartCardApp->owned_land        = $request->owned_land??null;
            $farSmartCardApp->lease_land        = $request->lease_land??null;
            $farSmartCardApp->barga_land        = $request->barga_land??null;
            $farSmartCardApp->total_land        = $request->total_land??null;
            $farSmartCardApp->training_info     = $request->training_info??null;
            $farSmartCardApp->earnings          = $request->earnings??null;
            $farSmartCardApp->crop_plan         = $request->crop_plan??null;
            $farSmartCardApp->crop_plan_bn      = $request->crop_plan_bn??null;

            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
            }
            $farSmartCardApp->attachment        = $attachment_name??null;

            if($farSmartCardApp->save()){
                GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
            }

            /*+++Irrigation Payment ++*/
            $transaction_no                          = strtoupper(uniqid());
            $irrigation_paymnet                      = new IrrigationPayment();
            $irrigation_paymnet->master_payment_id   = $request->master_payment_id;
            $irrigation_paymnet->payment_type_id     = 1;
            $irrigation_paymnet->org_id              = (int)$request->org_id;
            $irrigation_paymnet->farmer_id           = user_id();
            $irrigation_paymnet->far_application_id  = $farSmartCardApp->id;
            $irrigation_paymnet->application_type    = 3;
            $irrigation_paymnet->amount              = $request->amount;
            $irrigation_paymnet->trnx_currency       = "BDT";
            $irrigation_paymnet->transaction_no      = $transaction_no;
            $irrigation_paymnet->status              = 1;
            $irrigation_paymnet->pay_status          = "pending";
            $irrigation_paymnet->save();

            save_log([
                'data_id'   => $irrigation_paymnet->id,
                'table_name'=> 'irrigation_payments'
            ]);

            save_log([
                'data_id' => $farSmartCardApp->id,
                'table_name' => 'far_smart_card_apps',
            ]);

            DB::commit();

            if($request->final_pay == 1) {
                $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();

                $pay_info['s_uri']          = config('app.base_url.project_url').'smart-card-application/success-other';
                $pay_info['f_uri']          = config('app.base_url.project_url').'smart-card-application/decline';
                $pay_info['c_uri']          = config('app.base_url.project_url').'smart-card-application/cancel';
                $pay_info['cust_id']        = (int)user_id();
                $pay_info['cust_name']      = $farSmartCardApp->name;
                $pay_info['cust_mobo_no']   = $basic_info->mobile_no;
                $pay_info['cust_email']     = $basic_info->email;
                $pay_info['cust_mail_addr'] = $basic_info->far_village;

                $pay_info['trnx_id']        = $transaction_no;
                $pay_info['trnx_amt']       =  $request->amount;
                $pay_info['trnx_currency']  = 'BDT';
                $pay_info['ord_id']         = $irrigation_paymnet->id;
                $pay_info['ord_det']        = date('Y-m-d');

                $ekpay_payment = new EkpayLibrary();
                return $ekpay_payment->ekpay_payment($pay_info);
            } else {
                return response([
                    'success' => true,
                    'message' => 'Data save successfully',
                    'data'    => $irrigation_paymnet
                ]);
            }
        } catch (\Exception $ex) {
            DB::rollback();
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }
    }

    public function approvalList(Request $request)
    {
        $query = DB::table('far_smart_card_apps')
        ->leftJoin('far_smart_card_review','far_smart_card_apps.id', '=','far_smart_card_review.far_smart_card_apps_id')
        ->leftJoin('far_smart_card_rejects','far_smart_card_apps.id', '=','far_smart_card_rejects.far_smart_card_apps_id')
        ->select('far_smart_card_apps.*',
                'far_smart_card_review.note as review_note',
                'far_smart_card_review.note_bn as review_note_bn',
                'far_smart_card_review.created_at as review_created_at',
                'far_smart_card_rejects.reject_note',
                'far_smart_card_rejects.reject_note_bn',
                'far_smart_card_rejects.created_at as reject_created_at'
            );

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
            $query = $query->where('father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
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

        $query = $query->where('far_smart_card_apps.status', 3)
                        ->orderBy('far_smart_card_apps.id', 'desc');

        $list = $query->paginate($request->per_page??10);

        if(count( $list)>0){
            return response([
                'success' => true,
                'message' => 'Smart card application list',
                'data' => $list
            ]);
        }else{
            return response([
                'success' => false,
                'message' => 'Data not found!!'
            ]);
        }


    }

    public function cardgeneratedlist(Request $request)
    {
        $query = DB::table('far_smart_card_apps')
        ->leftJoin('far_smart_card_review','far_smart_card_apps.id', '=','far_smart_card_review.far_smart_card_apps_id')
        ->leftJoin('far_smart_card_rejects','far_smart_card_apps.id', '=','far_smart_card_rejects.far_smart_card_apps_id')
        ->select('far_smart_card_apps.*',
                'far_smart_card_review.note as review_note',
                'far_smart_card_review.note_bn as review_note_bn',
                'far_smart_card_review.created_at as review_created_at',
                'far_smart_card_rejects.reject_note',
                'far_smart_card_rejects.reject_note_bn',
                'far_smart_card_rejects.created_at as reject_created_at'
                );

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
            $query = $query->where('father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
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
        $query = $query->where('far_smart_card_apps.status', 4)
                ->orderBy('created_at', 'desc');

        $list = $query->paginate($request->per_page??10);

        if(count( $list)>0){
            return response([
                'success' => true,
                'message' => 'Smart card application list',
                'data' => $list
            ]);
        }else{
            return response([
                'success' => false,
                'message' => 'Data not found!!'
            ]);
        }


    }

    /**
     * Smart Card Application update
     */
    public function update(Request $request, $id)
    {
        $validationResult = FarmerSmartCardApplicationValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path  = 'smart-card-apps';
        $attachment =  $request->file('attachment');

        $farSmartCardApp = FarmerSmartCardApplication::find($id);
        $old_file = $farSmartCardApp->attachment;

        if (!$farSmartCardApp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

         DB::beginTransaction();
        try {

            $farSmartCardApp->org_id            = (int)$request->org_id;
            $farSmartCardApp->email             = $request->email;
            $farSmartCardApp->name              = $request->name;
            $farSmartCardApp->name_bn           = $request->name_bn;
            $farSmartCardApp->father_name       = $request->father_name;
            $farSmartCardApp->father_name_bn    = $request->father_name_bn;
            $farSmartCardApp->mother_name       = $request->mother_name;
            $farSmartCardApp->mother_name_bn    = $request->mother_name_bn;
            $farSmartCardApp->marital_status    = $request->marital_status;
            $farSmartCardApp->spouse_name       = $request->spouse_name != 'null' ? $request->spouse_name : null;
            $farSmartCardApp->spouse_name_bn    = $request->spouse_name_bn != 'null' ? $request->spouse_name_bn : null;
            $farSmartCardApp->no_of_child       = $request->no_of_child??0;
            $farSmartCardApp->nid               = $request->nid;
            $farSmartCardApp->mobile_no         = $request->mobile_no;
            $farSmartCardApp->gender            = $request->gender;
            $farSmartCardApp->far_division_id   = (int)$request->far_division_id;
            $farSmartCardApp->far_district_id   = (int)$request->far_district_id;
            $farSmartCardApp->far_upazilla_id   = (int)$request->far_upazilla_id;
            $farSmartCardApp->far_union_id      = (int)$request->far_union_id;
            $farSmartCardApp->office_id         = (int)$request->office_id;
            $farSmartCardApp->far_village       = $request->far_village;
            $farSmartCardApp->far_village_bn    = $request->far_village_bn;
            $farSmartCardApp->ward_no           = $request->ward_no;
            $farSmartCardApp->date_of_birth     = $request->date_of_birth;
            $farSmartCardApp->qualification     = $request->qualification;
            $farSmartCardApp->owned_land        = $request->owned_land;
            $farSmartCardApp->lease_land        = $request->lease_land;
            $farSmartCardApp->barga_land        = $request->barga_land;
            $farSmartCardApp->total_land        = $request->total_land;
            $farSmartCardApp->training_info     = $request->training_info;
            $farSmartCardApp->earnings          = $request->earnings;
            $farSmartCardApp->crop_plan         = $request->crop_plan;
            $farSmartCardApp->crop_plan_bn      = $request->crop_plan_bn;
            $farSmartCardApp->status            = 1;
            $farSmartCardApp->reissue_status    = $request->reissue_status == 2 ? 2 : 1;

            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request,$file_path,'attachment');

                $farSmartCardApp->attachment    =  $attachment_name;

                if($farSmartCardApp->update()){
                     GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name, $old_file );
                }
            }else{
                $farSmartCardApp->update();
            }


            $irrigation_paymnet = IrrigationPayment::find($request->payment_id);
            if ($irrigation_paymnet) {
                $irrigation_paymnet->master_payment_id   = $request->master_payment_id;
                $irrigation_paymnet->amount   = $request->amount;
                $irrigation_paymnet->save();
            }

            DB::commit();

            save_log([
                'data_id' => $farSmartCardApp->id,
                'table_name' => 'far_smart_card_apps',
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
            'data'    => $farSmartCardApp
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

        if ($request->is_bypass == 1) {
            $ekpay_payment = new EkpayLibrary();
            return $ekpay_payment->defaultSuccess($transaction_no);
        }

        $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();

        $pay_info['s_uri']          = config('app.base_url.project_url').'smart-card-application/success-other';
        $pay_info['f_uri']          = config('app.base_url.project_url').'smart-card-application/decline';
        $pay_info['c_uri']          = config('app.base_url.project_url').'smart-card-application/cancel';
        $pay_info['cust_id']        = (int)user_id();
        $pay_info['cust_name']      = $basic_info->name;
        $pay_info['cust_mobo_no']   = username();
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
    public function reissuePayment (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'far_application_id'        => 'required',
            'master_payment_id'         => 'required',
            'amount'                    => 'required',
            'payment_type_id'           => 'required'
        ]);
        if ($validator->fails()) {
           return ([
               'success' => false,
               'errors' => $validator->errors()
           ]);

        } else {
            DB::beginTransaction();

            try {

                $IrrigationPaymentOld = IrrigationPayment::where('far_application_id', $request->far_application_id)
                                    ->where('org_id', $request->org_id)
                                    ->where('application_type',3)
                                    ->where('payment_type_id',2)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->master_payment_id   = $request->master_payment_id;
                    $IrrigationPaymentOld->org_id              = $request->org_id;
                    $IrrigationPaymentOld->amount              = $request->amount;
                    $IrrigationPaymentOld->transaction_no      = $transaction_no;
                    $IrrigationPaymentOld->save();

                    $payment_id = $IrrigationPaymentOld->id;

                } else {

                    $irrigation_payment                      = new IrrigationPayment();
                    $irrigation_payment->master_payment_id   = $request->master_payment_id;
                    $irrigation_payment->org_id              = $request->org_id;
                    $irrigation_payment->farmer_id           = user_id();
                    $irrigation_payment->far_application_id  = $request->far_application_id;
                    $irrigation_payment->application_type    = 3;
                    $irrigation_payment->payment_type_id     = 2;
                    $irrigation_payment->amount              = $request->amount;
                    $irrigation_payment->trnx_currency       = "BDT";
                    $irrigation_payment->transaction_no      = $transaction_no;
                    $irrigation_payment->status              = 1;
                    $irrigation_payment->pay_status          = "success";
                    $irrigation_payment->save();

                    save_log([
                        'data_id'   => $irrigation_payment->id,
                        'table_name'=> 'irrigation_payments'
                    ]);

                    $payment_id = $irrigation_payment->id;

                }

                DB::commit();
                if ($request->is_bypass == 1) {
                    $ekpay_payment = new EkpayLibrary();
                    return $ekpay_payment->defaultSuccess($transaction_no);
                }

                $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();

                $pay_info['s_uri']          = config('app.base_url.project_url').'pump-operator-application/success-other';
                $pay_info['f_uri']          = config('app.base_url.project_url').'pump-operator-application/decline';
                $pay_info['c_uri']          = config('app.base_url.project_url').'pump-operator-application/cancel';
                $pay_info['cust_id']        = (int)user_id();
                $pay_info['cust_name']      = $basic_info->name;
                $pay_info['cust_mobo_no']   = username();
                $pay_info['cust_email']     = $basic_info->email;
                $pay_info['cust_mail_addr'] = $basic_info->far_village;
                $pay_info['trnx_id']        = $transaction_no;
                $pay_info['trnx_amt']       = $request->amount;
                $pay_info['trnx_currency']  = 'BDT';
                $pay_info['ord_id']         = $payment_id;
                $pay_info['ord_det']        = date('Y-m-d');

                $ekpay_payment = new EkpayLibrary();
                return $ekpay_payment->ekpay_payment($pay_info);

                return response([
                    'success' => true,
                    'message' => 'Data save successfully',
                    'data'    => []
                ]);

            } catch (\Exception $ex) {
                DB::rollback();
                return response([
                    'success' => false,
                    'message' => 'Failed to save data.',
                    'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
                ]);
            }

        }

    }
    /**
    * Smart card application statas update as processing
    */
    public function updateAsProcessing($id)
    {
        $smartCardProcessing = FarmerSmartCardApplication::find($id);

        if (!$smartCardProcessing) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $smartCardProcessing->status = 2;
        $smartCardProcessing->update();

        save_log([
            'data_id' => $smartCardProcessing->id,
            'table_name' => 'far_smart_card_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Smart card application statas update as processing',
            'data'    => $smartCardProcessing
        ]);
    }

     /**
     * Smart card application statas update as approved
     */
    public function updateAsApproved($id)
    {
        $smartCardApproved = FarmerSmartCardApplication::find($id);

        if (!$smartCardApproved) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $smartCardApproved->status = 3;
        $smartCardApproved->update();

        save_log([
            'data_id' => $smartCardApproved->id,
            'table_name' => 'far_smart_card_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Smart card application statas update as approved',
            'data'    => $smartCardApproved
        ]);
    }

    /**
     * Smart card application statas update as generated
     */
    public function updateAsGenerated($id)
    {
        $smartCardGenerated = FarmerSmartCardApplication::find($id);
        $smartCardGenerated->status = 4;
        $smartCardGenerated->update();

        save_log([
            'data_id' => $smartCardGenerated->id,
            'table_name' => 'far_smart_card_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Smart card application statas update as generated',
            'data'    => $smartCardGenerated
        ]);
    }

    /**
     * Smart card application statas update as generated
     */
    public function updateAsGeneratedAll(Request $request)
    {
        // return $request->app_ids;
        $ids = $request->app_ids;
        foreach ($ids as $key => $value) {
            $smartCardGenerated = FarmerSmartCardApplication::find($value);
            $smartCardGenerated->status = 3;
            $smartCardGenerated->update();
        }

        save_log([
            'data_id' => $smartCardGenerated->id,
            'table_name' => 'far_smart_card_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Smart card application statas update as generated',
            'data'    => $smartCardGenerated
        ]);
    }

    /**
     * Smart card application statas update as reviewed
     */
    public function updateAsReviewed($id)
    {
        $smartCardReviewed = FarmerSmartCardApplication::find($id);

        if (!$smartCardReviewed) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $smartCardReviewed->status = 7;
        $smartCardReviewed->update();

        save_log([
            'data_id' => $smartCardReviewed->id,
            'table_name' => 'far_smart_card_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Smart card application statas update as reviewed',
            'data'    => $smartCardReviewed
        ]);
    }

    /**
     * Smart Card Application move to trush
     */
    public function moveToTrush($id)
    {
        $farSmartCardApp = FarmerSmartCardApplication::find($id);

        if (!$farSmartCardApp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $farSmartCardApp->delete();

        save_log([
            'data_id' => $id,
            'table_name' => 'far_smart_card_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

     /**
     * Smart Card Application restore from trush
     */
    public function restoreData($id)
    {
        $farSmartCardApp = FarmerSmartCardApplication::find($id);

        if (!$farSmartCardApp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $restoreSmartCardApp = FarmerSmartCardApplication::withTrashed()
                             ->where('id', $id)
                             ->restore();
        save_log([
            'data_id' => $id,
            'table_name' => 'far_smart_card_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data restore successfully'
        ]);
    }

    /**
     * Smart Card Application Info permanently Delete
    */
    public function permanentlyDelete($id)
    {
        $farSmartCardApp = FarmerSmartCardApplication::find($id);

        if (!$farSmartCardApp) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $cardReview = FarmerSmartCardReview::where('far_smart_card_apps_id',$id)->get();
        $cardRejects = FarmerSmartCardRejects::where('far_smart_card_apps_id',$id)->get();
        if($cardReview){
            FarmerSmartCardReview::where('far_smart_card_apps_id',$id)->delete();
        }
        if($cardRejects){
            FarmerSmartCardRejects::where('far_smart_card_apps_id',$id)->delete();
        }

        $permanentlyDelete = FarmerSmartCardApplication::withTrashed()
                         ->where('id', $id)
                         ->forceDelete();

        save_log([
            'data_id' => $id,
            'table_name' => 'far_smart_card_apps & far_smart_card_rejects & far_smart_card_review',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data permanently deleted successfully'
        ]);
    }

}
