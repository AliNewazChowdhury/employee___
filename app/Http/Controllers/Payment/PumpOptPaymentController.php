<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FarmerProfile\FarmerBasicInfos;
use App\Models\Payment\IrrigationPayment;
use App\Library\EkpayLibrary;
use Validator;
use DB;
use App\Models\PumpInstallation\SchemeParticipationFee;
use App\Models\PumpInstallation\SchemeSecurityMoney;
use App\Models\FarmerOperator\FarmerSchemeApplication;

class PumpOptPaymentController extends Controller
{
    public function applicationFee(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'far_application_id'        => 'required',
            'master_payment_id'            => 'required',
            'amount'                       => 'required'
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
                                    ->where('application_type',2)
                                    ->where('payment_type_id',1)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->master_payment_id  = $request->master_payment_id;
                    $IrrigationPaymentOld->amount             = $request->amount;
                    $IrrigationPaymentOld->org_id             = $request->org_id;
                    $IrrigationPaymentOld->transaction_no     = $transaction_no;
                    $IrrigationPaymentOld->save();

                    $payment_id = $IrrigationPaymentOld->id;

                } else {

                    $irrigation_payment                      = new IrrigationPayment();
                    $irrigation_payment->master_payment_id   = $request->master_payment_id;
                    $irrigation_payment->farmer_id           = user_id();
                    $irrigation_payment->org_id              = $request->org_id;
                    $irrigation_payment->far_application_id  = $request->far_application_id;
                    $irrigation_payment->application_type    = 2;
                    $irrigation_payment->payment_type_id     = 1;
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
    public function renewFee(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'far_application_id'        => 'required',
            'master_payment_id'            => 'required',
            'amount'                       => 'required'
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
                                    ->where('application_type',2)
                                    ->where('payment_type_id',2)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->master_payment_id = $request->master_payment_id;
                    $IrrigationPaymentOld->amount            = $request->amount;
                    $irrigation_payment->org_id              = $request->org_id;
                    $IrrigationPaymentOld->transaction_no    = $transaction_no;
                    $IrrigationPaymentOld->save();

                    $payment_id = $IrrigationPaymentOld->id;

                } else {

                    $irrigation_payment                      = new IrrigationPayment();
                    $irrigation_payment->master_payment_id   = $request->master_payment_id;
                    $irrigation_payment->farmer_id           = user_id();
                    $irrigation_payment->org_id              = $request->org_id;
                    $irrigation_payment->far_application_id  = $request->far_application_id;
                    $irrigation_payment->application_type    = 2;
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
    public function SecurityMoney(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'far_application_id'        => 'required',
            'master_payment_id'            => 'required',
            'amount'                       => 'required'
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
                                    ->where('application_type',2)
                                    ->where('payment_type_id',3)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->master_payment_id   = $request->master_payment_id;
                    $IrrigationPaymentOld->gender              = $request->gender;
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
                    $irrigation_payment->application_type    = 2;
                    $irrigation_payment->payment_type_id     = 3;
                    $irrigation_payment->amount              = $request->amount;
                    $irrigation_payment->trnx_currency       = "BDT";
                    $irrigation_payment->transaction_no      = $transaction_no;
                    $irrigation_payment->status              = 1;
                    $irrigation_payment->gender              = $request->gender;
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
}
