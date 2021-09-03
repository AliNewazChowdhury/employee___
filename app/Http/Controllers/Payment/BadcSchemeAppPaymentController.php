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


class BadcSchemeAppPaymentController extends Controller
{
    
    public function badcFormFeePayment(Request $request)
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
                                    ->where('org_id',3)
                                    ->where('scheme_type_id',$request->scheme_type_id)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {
                    $IrrigationPaymentOld->master_payment_id   = $request->master_payment_id;
                    $IrrigationPaymentOld->application_type    = 1;
                    $IrrigationPaymentOld->org_id              = 3;
                    $IrrigationPaymentOld->scheme_type_id      = $request->scheme_type_id;
                    $IrrigationPaymentOld->amount              = $request->amount;
                    $IrrigationPaymentOld->transaction_no      = $transaction_no;
                    $IrrigationPaymentOld->status              = 1;
                    $IrrigationPaymentOld->save();
                    $payment_id = $IrrigationPaymentOld->id;

                } else {

                    $irriSchemePayment                         = new IrrigationPayment();
                    $irriSchemePayment->master_payment_id      = $request->master_payment_id;
                    $irriSchemePayment->application_type       = 1;
                    $irriSchemePayment->org_id                 = 3;
                    $irriSchemePayment->farmer_id              = user_id();
                    $irriSchemePayment->far_application_id     = $request->scheme_application_id;
                    $irriSchemePayment->payment_type_id        = 1;
                    $irriSchemePayment->scheme_type_id         = $request->scheme_type_id;
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
                        'table_name'=> 'irrigation_payments'
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
    public function badcPartFeePayment(Request $request)
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
                                    ->where('org_id',3)
                                    ->where('application_type',1)
                                    ->where('payment_type_id',2)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->amount              = $request->amount;
                    $IrrigationPaymentOld->transaction_no      = $transaction_no;
                    $IrrigationPaymentOld->scheme_participation_fee_id   =  json_encode($request->scheme_participation_fee_id);
                    $IrrigationPaymentOld->status              = 1;
                    $IrrigationPaymentOld->save();
                    $payment_id = $IrrigationPaymentOld->id;
                } else {

                    $irriSchemePayment                                = new IrrigationPayment();
                    $irriSchemePayment->org_id                        = 3;
                    $irriSchemePayment->application_type              = 1;
                    $irriSchemePayment->farmer_id                     = user_id();
                    $irriSchemePayment->far_application_id            = $request->scheme_application_id;
                    $irriSchemePayment->scheme_participation_fee_id   =  json_encode($request->scheme_participation_fee_id);
                    $irriSchemePayment->payment_type_id               = 2;
                    $irriSchemePayment->amount                        = $request->amount;
                    $irriSchemePayment->mac_addr                      = strtok(exec("getmac"), ' '); 
                    $irriSchemePayment->trnx_currency                 = "BDT";
                    $irriSchemePayment->transaction_no                = $transaction_no;
                    $irriSchemePayment->status                        = 1;
                    $irriSchemePayment->pay_status                    = "pending";
                    $irriSchemePayment->save(); 
                    $payment_id = $irriSchemePayment->id;

                    save_log([
                        'data_id'   => $irriSchemePayment->id,
                        'table_name'=> 'irrigation_payments'
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

                $this->defaultSuccess($transaction_no);

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
    public function badcPartFeePaymentDue(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'scheme_application_id'          => 'required',
            'amount'                         => 'required',
            'scheme_participation_fee_id'    => 'required'
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
                                    ->where('org_id', 3)
                                    ->where('application_type', 1)
                                    ->where('payment_type_id', 2)
                                    ->where('scheme_participation_fee_id', json_encode($request->scheme_participation_fee_id))
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());
                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->amount              = $request->amount;
                    $IrrigationPaymentOld->transaction_no      = $transaction_no;
                    $IrrigationPaymentOld->scheme_participation_fee_id   =  json_encode($request->scheme_participation_fee_id);
                    $IrrigationPaymentOld->status              = 1;
                    $IrrigationPaymentOld->save();
                    $payment_id = $IrrigationPaymentOld->id;
                } else {

                    $irriSchemePayment                                = new IrrigationPayment();
                    $irriSchemePayment->org_id                        = 3;
                    $irriSchemePayment->farmer_id                     = user_id();
                    $irriSchemePayment->application_type              = 1;
                    $irriSchemePayment->far_application_id            = $request->scheme_application_id;
                    $irriSchemePayment->scheme_participation_fee_id   = json_encode($request->scheme_participation_fee_id);
                    $irriSchemePayment->payment_type_id               = 2;
                    $irriSchemePayment->amount                        = $request->amount;
                    $irriSchemePayment->mac_addr                      = strtok(exec("getmac"), ' '); 
                    $irriSchemePayment->trnx_currency                 = "BDT";
                    $irriSchemePayment->transaction_no                = $transaction_no;
                    $irriSchemePayment->status                        = 1;
                    $irriSchemePayment->pay_status                    = "pending";
                    $irriSchemePayment->save(); 
                    $payment_id = $irriSchemePayment->id;

                    save_log([
                        'data_id'   => $irriSchemePayment->id,
                        'table_name'=> 'irrigation_payments'
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
    public function badcSecurityFeePayment(Request $request)
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
                                    ->where('org_id', 3)
                                    ->where('application_type',1)
                                    ->where('payment_type_id',3)
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());

                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->amount                     = $request->amount;
                    $IrrigationPaymentOld->transaction_no             = $transaction_no;
                    $IrrigationPaymentOld->scheme_security_money_id   =  json_encode($request->scheme_security_money_id);
                    $IrrigationPaymentOld->status                     = 1;
                    $IrrigationPaymentOld->save();
                    $payment_id = $IrrigationPaymentOld->id;
                } else {

                    $irriSchemePayment                                = new IrrigationPayment();
                    $irriSchemePayment->farmer_id                     = user_id();
                    $irriSchemePayment->org_id                        = 3;
                    $irriSchemePayment->application_type              = 1;
                    $irriSchemePayment->far_application_id            = $request->scheme_application_id;
                    $irriSchemePayment->scheme_security_money_id      = json_encode($request->scheme_security_money_id);
                    $irriSchemePayment->payment_type_id               = 3;
                    $irriSchemePayment->amount                        = $request->amount;
                    $irriSchemePayment->mac_addr                      = strtok(exec("getmac"), ' '); 
                    $irriSchemePayment->trnx_currency                 = "BDT";
                    $irriSchemePayment->transaction_no                = $transaction_no;
                    $irriSchemePayment->status                        = 1;
                    $irriSchemePayment->pay_status                    = "pending";
                    $irriSchemePayment->save(); 
                    $payment_id = $irriSchemePayment->id;

                    save_log([
                        'data_id'   => $irriSchemePayment->id,
                        'table_name'=> 'irrigation_payments'
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
    public function badcSecurityFeePaymentDue(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'scheme_application_id'          => 'required',
            'amount'                         => 'required',
            'scheme_security_money_id'    => 'required'
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
                                    ->where('org_id',3)
                                    ->where('application_type',1)
                                    ->where('payment_type_id',3)
                                    ->where('scheme_security_money_id', json_encode($request->scheme_security_money_id))
                                    ->where('pay_status', 'pending')->first();

                $transaction_no = strtoupper(uniqid());
                if ($IrrigationPaymentOld) {

                    $IrrigationPaymentOld->amount              = $request->amount;
                    $IrrigationPaymentOld->transaction_no      = $transaction_no;
                    $IrrigationPaymentOld->scheme_security_money_id   =  json_encode($request->scheme_security_money_id);
                    $IrrigationPaymentOld->status              = 1;
                    $IrrigationPaymentOld->save();
                    $payment_id = $IrrigationPaymentOld->id;
                } else {

                    $irriSchemePayment                                = new IrrigationPayment();
                    $irriSchemePayment->farmer_id                     = user_id();
                    $irriSchemePayment->org_id                        = 3;
                    $irriSchemePayment->application_type              = 1;
                    $irriSchemePayment->far_application_id            = $request->scheme_application_id;
                    $irriSchemePayment->scheme_security_money_id      = json_encode($request->scheme_security_money_id);
                    $irriSchemePayment->payment_type_id               = 3;
                    $irriSchemePayment->amount                        = $request->amount;
                    $irriSchemePayment->mac_addr                      = strtok(exec("getmac"), ' ');
                    $irriSchemePayment->trnx_currency                 = "BDT";
                    $irriSchemePayment->transaction_no                = $transaction_no;
                    $irriSchemePayment->status                        = 1;
                    $irriSchemePayment->pay_status                    = "pending";
                    $irriSchemePayment->save(); 
                    $payment_id = $irriSchemePayment->id;

                    save_log([
                        'data_id'   => $irriSchemePayment->id,
                        'table_name'=> 'irrigation_payments'
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
                        $scheme_part_fees = json_decode($irrigation_payment->scheme_participation_fee_id);
                        foreach ($scheme_part_fees as $fee) {
                            $scheme_part_fee_single = SchemeParticipationFee::find($fee->id);
                            $scheme_part_fee_single->payment_status = 2;
                            $scheme_part_fee_single->update();
                        }
                        $FarSchApplication = FarmerSchemeApplication::find($irrigation_payment->far_application_id);
                        $FarSchApplication->status = 8;
                        $FarSchApplication->save();
                    }

                    if ($irrigation_payment->payment_type_id == 3) {
                        $scheme_security_fees = json_decode($irrigation_payment->scheme_security_money_id);
                        foreach ($scheme_security_fees as $fee) {
                            $scheme_security_fee_single = SchemeSecurityMoney::find($fee->id);
                            $scheme_security_fee_single->payment_status = 2;
                            $scheme_security_fee_single->update();
                        }
                        $FarSchApplication = FarmerSchemeApplication::find($irrigation_payment->far_application_id);
                        $FarSchApplication->status = 8;
                        $FarSchApplication->save();
                        
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
