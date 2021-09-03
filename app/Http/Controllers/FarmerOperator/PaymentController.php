<?php

namespace App\Http\Controllers\FarmerOperator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\FarmerOperator\PaymentValidation;
use App\Models\FarmerOperator\FarEkpayPayment;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use DB;
class PaymentController extends Controller
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
     * get all Scheme project
     */
    public function index(Request $request)
    {
        $query = DB::table('far_ekpay_payments')
                ->join('far_scheme_application','far_ekpay_payments.far_scheme_application_id', '=','far_scheme_application.id')
                ->select('far_ekpay_payments.*',
                        'far_scheme_application.name as farmer_name','far_scheme_application.name_bn as farmer_name_bn');

        

        $list = $query->paginate($request->per_page);

        return response([
            'success' => true,
            'message' => 'Paymnet list data',
            'data' => $list
        ]);
    }

    /**
     * Scheme project  store
     */
    public function store(Request $request)
    {   
        $validationResult = PaymentValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }


        $far_scheme_application_id = $request->far_scheme_application_id;
        $master_payment_id = $request->master_payment_id;
        $amount = $request->amount;
        $user_id = user_id();
        $trnx_currency = "BDT"; 
        $transaction_no = strtoupper(uniqid());
        $pay_status = 'pending';




        try {

            DB::beginTransaction();                
            $far_ekpay_payment                             = new FarEkpayPayment();
            $far_ekpay_payment->master_payment_id     = (int)$request->master_payment_id;
            $far_ekpay_payment->far_scheme_application_id  = (int)$request->far_scheme_application_id;
            $far_ekpay_payment->amount                     = (int)$request->amount;
            $far_ekpay_payment->user_id                    = (int)$user_id;
            $far_ekpay_payment->transaction_no             = $transaction_no;
            $far_ekpay_payment->pay_status                 = $pay_status;
            $far_ekpay_payment->save();

            $FarmerSchemeApplication = FarmerSchemeApplication::find($far_scheme_application_id);
            $FarmerSchemeApplication->payment_status = 1;
            DB::commit();


            save_log([
                'data_id'   => $far_ekpay_payment->id,
                'table_name'=> 'far_ekpay_payments'
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
            'data'    => $far_ekpay_payment
        ]);
    }

    /**
     * Scheme project update
     */
    public function update(Request $request, $id)
    {
        $validationResult = PaymentValidation::validate($request, $id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $far_ekpay_payment = FarExpayPayment::find($id);

        if (!$far_ekpay_payment) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $far_scheme_application_id = $request->far_scheme_application_id;
        $master_payment_id = $request->master_payment_id;
        $amount = $request->amount;
        $user_id = user_id();
        $trnx_currency = "BDT";
        $transaction_no = tranaction_id();
        $pay_status = 'pending';

        try {
            $far_ekpay_payment                             = new FarExpayPayment();
            $far_ekpay_payment->master_payment_id     = (int)$request->master_payment_id;
            $far_ekpay_payment->far_scheme_application_id  = (int)$request->far_scheme_application_id;
            $far_ekpay_payment->amount                     = (int)$request->amount;
            $far_ekpay_payment->user_id                    =  (int)$user_id;
            $far_ekpay_payment->transaction_no             = $transaction_no;
            $far_ekpay_payment->pay_status                 = $pay_status;
            $far_ekpay_payment->save();

            save_log([
                'data_id'       => $scheme_project->id,
                'table_name'    => 'far_ekpay_payments',
                'execution_type'=> 1
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $scheme_project
        ]);
    }

    /**
     * Scheme project destroy
     */
    public function destroy($id)
    {
        $scheme_project = FarExpayPayment::find($id);

        if (!$scheme_project) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $scheme_project->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_ekpay_payments',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
