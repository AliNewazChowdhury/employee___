<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FarmerProfile\FarmerBasicInfos;
use App\Models\Payment\IrrigationPayment;
use App\Library\EkpayLibrary;
use Validator;
use DB;
use App\Models\FarmerOperator\FarmerSchemeApplication;


class SchemeAppFormFeeOtherController extends Controller
{
    public function FormFee(Request $request) {

        $validator = Validator::make($request->all(), [
            'org_id' => 'required',
            'scheme_application_id' => 'required',
            'master_payment_id'     => 'required',
            'amount'                => 'required'
        ]);
        if ($validator->fails()) {
           return ([
               'success' => false,
               'errors' => $validator->errors()
           ]);

        } else {
            DB::beginTransaction();

            try {

                $IrrigationPaymentOld = IrrigationPayment::where('far_application_id', $request->scheme_application_id)
                                    ->where('org_id', $request->org_id)
                                    ->where('application_type', 1)
                                    ->where('payment_type_id', 0)
                                    ->where('scheme_type_id', 0)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->master_payment_id      = $request->master_payment_id;
                    $IrrigationPaymentOld->org_id                 = $request->org_id;
                    $IrrigationPaymentOld->amount                 = $request->amount;
                    $IrrigationPaymentOld->transaction_no         = $transaction_no;
                    $IrrigationPaymentOld->status                 = 1;
                    $IrrigationPaymentOld->save();
                    $payment_id = $IrrigationPaymentOld->id;

                } else {

                    $irriSchemePayment                         = new IrrigationPayment();
                    $irriSchemePayment->master_payment_id      = $request->master_payment_id;
                    $irriSchemePayment->application_type       = 1;
                    $irriSchemePayment->org_id                 = $request->org_id;
                    $irriSchemePayment->payment_type_id        = 0;
                    $irriSchemePayment->scheme_type_id         = 0;
                    $irriSchemePayment->farmer_id              = user_id();
                    $irriSchemePayment->far_application_id     = $request->scheme_application_id;
                    $irriSchemePayment->amount                 = $request->amount;
                    $irriSchemePayment->mac_addr               = strtok(exec("getmac"), ' '); 
                    $irriSchemePayment->trnx_currency          = "BDT";
                    $irriSchemePayment->transaction_no         = $transaction_no;
                    $irriSchemePayment->status                 = 1;  
                    $irriSchemePayment->pay_status             = "pending";
                    $irriSchemePayment->save(); 
                    $payment_id = $irriSchemePayment->id;

                    save_log([
                        'data_id'   => $irriSchemePayment->id,
                        'table_name'=> 'irri_scheme_payments'
                    ]);
                }

                DB::commit();
                
                if ($request->is_bypass == 1) {
                    $ekpay_payment = new EkpayLibrary();
                    return $ekpay_payment->defaultSuccessBadc($transaction_no);
                }
                
                $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();
            
                $pay_info['s_uri']          = config('app.base_url.project_url').'scheme-application/success-badc';
                $pay_info['f_uri']          = config('app.base_url.project_url').'scheme-application/decline';
                $pay_info['c_uri']          = config('app.base_url.project_url').'scheme-application/cancel';
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
}
