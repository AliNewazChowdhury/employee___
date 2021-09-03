<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment\IrrigationPayment;
use App\Library\EkpayLibrary;
use Validator;
use DB;
use App\Models\PumpInstallation\SchemeParticipationFee;
use App\Models\PumpInstallation\SchemeSecurityMoney;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\FarmerProfile\FarmerBasicInfos;

class BmdaSchemeAppPaymentController extends Controller
{
    public function BmdaFormFeePayment(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'scheme_application_id'        => 'required',
            'payment_type_id'        => 'required',
            'scheme_type_id'        => 'required',
            'master_payment_id'        => 'required',
            'amount'        => 'required'
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
                                    ->where('application_type',1)
                                    ->where('payment_type_id',1)
                                    ->where('org_id', 15)
                                    ->where('scheme_type_id',$request->scheme_type_id)
                                    ->where('pay_status', 'pending')->first();
                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->master_payment_id   = $request->master_payment_id;
                    $IrrigationPaymentOld->org_id              = 15;
                    $IrrigationPaymentOld->scheme_type_id      = $request->scheme_type_id;
                    $IrrigationPaymentOld->amount              = $request->amount;
                    $IrrigationPaymentOld->transaction_no      = $transaction_no;
                    $IrrigationPaymentOld->status              = 1;
                    $IrrigationPaymentOld->save();
                    $payment_id = $IrrigationPaymentOld->id;
                } else {

                    $irrigationPayment                         = new IrrigationPayment();
                    $irrigationPayment->master_payment_id      = $request->master_payment_id;
                    $irrigationPayment->application_type       = 1;
                    $irrigationPayment->farmer_id              = user_id();
                    $irrigationPayment->org_id                 = 15;
                    $irrigationPayment->far_application_id     = $request->scheme_application_id;
                    $irrigationPayment->payment_type_id        = 1;
                    $irrigationPayment->scheme_type_id         = $request->scheme_type_id;
                    $irrigationPayment->amount                 = $request->amount;
                    $irrigationPayment->mac_addr               = strtok(exec("getmac"), ' ');
                    $irrigationPayment->trnx_currency          = "BDT";
                    $irrigationPayment->transaction_no         = $transaction_no;
                    $irrigationPayment->status                 = 1;
                    $irrigationPayment->pay_status             = "pending";
                    $irrigationPayment->save(); 
                    $payment_id = $irrigationPayment->id;

                    save_log([
                        'data_id'   => $irrigationPayment->id,
                        'table_name'=> 'irrigation_payments'
                    ]);
                }
              
                DB::commit();  
                if ($request->is_bypass == 1) {
                    $ekpay_payment = new EkpayLibrary();
                    return $ekpay_payment->defaultSuccessBmda($transaction_no);
                } 

                $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();
            
                $pay_info['s_uri']          = config('app.base_url.project_url').'scheme-application/success-bmda';
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
    public function BmdaPartFeePayment(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'scheme_application_id'   => 'required',
            'amount'                  => 'required'
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
                                    ->where('application_type',1)
                                    ->where('payment_type_id',2)
                                    ->where('org_id', 15)
                                    ->where('circle_area_id', $request->circle_area_id)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->amount                        = $request->amount;
                    $IrrigationPaymentOld->transaction_no                = $transaction_no;
                    $IrrigationPaymentOld->circle_area_id                = $request->circle_area_id;
                    $IrrigationPaymentOld->scheme_participation_fee_id   = $request->scheme_participation_fee_id;
                    $IrrigationPaymentOld->status                        = 1;
                    $IrrigationPaymentOld->pay_status                    = "pending";
                    $IrrigationPaymentOld->save();
                    $payment_id = $IrrigationPaymentOld->id;
                } else {

                    $irrigationPayment                                = new IrrigationPayment();
                    $irrigationPayment->farmer_id                     = user_id();
                    $irrigationPayment->org_id                        = 15;
                    $irrigationPayment->application_type              = 1;
                    $irrigationPayment->far_application_id            = $request->scheme_application_id;
                    $irrigationPayment->scheme_participation_fee_id   = $request->scheme_participation_fee_id;
                    $irrigationPayment->payment_type_id               = 2;
                    $irrigationPayment->circle_area_id                = $request->circle_area_id;
                    $irrigationPayment->amount                        = $request->amount;
                    $irrigationPayment->mac_addr                      = strtok(exec("getmac"), ' ');
                    $irrigationPayment->trnx_currency                 = "BDT";
                    $irrigationPayment->transaction_no                = $transaction_no;
                    $irrigationPayment->status                        = 1;
                    $irrigationPayment->pay_status                    = "pending";
                    $irrigationPayment->save(); 
                    $payment_id = $irrigationPayment->id;

                    save_log([
                        'data_id'   => $irrigationPayment->id,
                        'table_name'=> 'irrigation_payments'
                    ]);
                }

                DB::commit();
                if ($request->is_bypass == 1) {
                    $ekpay_payment = new EkpayLibrary();
                    return $ekpay_payment->defaultSuccessBmda($transaction_no);
                } 

                $basic_info = FarmerBasicInfos::whereFarmerId(user_id())->first();
            
                $pay_info['s_uri']          = config('app.base_url.project_url').'scheme-application/success-bmda';
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
    public function defaultSuccess ($trnsId) {
        if(!empty($trnsId)){

            $irrigation_payment = IrrigationPayment::where('transaction_no', $trnsId)->first();
            if ($irrigation_payment && $irrigation_payment->status == 1) {

                try {
                    $irrigation_payment->status     = 2;
                    $irrigation_payment->pay_status = 'success';
                    $irrigation_payment->update();
    
                    if ($irrigation_payment->payment_type_id == 2) {
                        
                        $scheme_part_fee_single = SchemeParticipationFee::find($irrigation_payment->scheme_participation_fee_id);
                        $scheme_part_fee_single->payment_status = 2;
                        $scheme_part_fee_single->update();
                        
                    }

                    return response([
                        'success' => true,
                        'message' => 'Payment paid successfully.'
                    ]);

                } catch (\Exception $ex) {

                    return response([
                        'success' => false,
                        'message' => 'Failed to save data.',
                        'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
                    ]);
                }
            } else {
                return response([
                    'success' => false,
                    'message' => 'Invalid Transaction Number.'
                ]);
            }
        }
    }
}
