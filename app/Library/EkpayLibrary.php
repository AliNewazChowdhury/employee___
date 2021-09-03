<?php
namespace App\Library;

use DB;
use App\Models\Payment\IrrigationPayment;
use App\Models\PumpInstallation\SchemeSecurityMoney;
use App\Models\SmartCard\FarmerSmartCardApplication;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\SchemeParticipationFee;
use App\Models\WaterTesting\FarmerWaterTestApplication;
use App\Models\PumpOperator\FarmerPumpOperatorApplication;
use App\Models\PumpOperator\FarmerPumpOperatorApplicationReniews;

class EkpayLibrary
{
    private $mer_reg_id = "mins_agri";
    private $mer_pas_key = "MinS@aGr3321";
    private $domain = "https://pg.ekpay.gov.bd/ekpaypg/v1?sToken=";
    //private $domain = "https://sandbox.ekpay.gov.bd/ekpaypg/v1?sToken=";
    private $ipn_channel = "0";
    private $ipn_email = 'ipn@ekpay.gov.bd';
    private $ipn_uri = "http://ekpay.gov.bd/v1/ipn/SendIpn";

    public function ekpay_payment($pay_info = [])
    {
    
        $ekp_arrya = array();
        $ekp_array["mer_info"]= array("mer_reg_id"=>$this->mer_reg_id, "mer_pas_key"=>$this->mer_pas_key);

        $ekp_array["req_timestamp"]=  date('Y-m-d H:i:s').' GMT+6';

        $ekp_array["feed_uri"]= array("s_uri"=>$pay_info['s_uri'], 
                                        "f_uri"=>$pay_info['f_uri'],
                                        "c_uri"=>$pay_info['c_uri']
                                    );

        $ekp_array["cust_info"]= array("cust_id"=>$pay_info['cust_id'], 
                                        "cust_name"=>$pay_info['cust_name'],
                                        "cust_mobo_no"=>$pay_info['cust_mobo_no'],
                                        "cust_email"=>$pay_info['cust_email'],
                                        "cust_mail_addr"=>$pay_info['cust_mail_addr']
                                    );

        $ekp_array["trns_info"]= array("trnx_id"=>$pay_info['trnx_id'], 
                                        "trnx_amt"=>$pay_info['trnx_amt'],
                                        "trnx_currency"=>"BDT",
                                        "ord_id"=>$pay_info['ord_id'],
                                        "ord_det"=>$pay_info['ord_det']
                                    );

        $ekp_array["ipn_info"]= array("ipn_channel"=>$this->ipn_channel,
                                        "ipn_email"=>$this->ipn_email,
                                        "ipn_uri"=>$this->ipn_uri
                                    );

        $MAC = exec("getmac"); 
	    $MAC = strtok($MAC, ' ');
        $ekp_array["mac_addr"]= '103.199.168.69';


        $adminUrl="https://pg.ekpay.gov.bd/ekpaypg/v1/merchant-api";
        //$adminUrl="https://sandbox.ekpay.gov.bd/ekpaypg/v1/merchant-api";

        try{
            $ch = curl_init();
            $data_string = json_encode($ekp_array);
            $ch = curl_init($adminUrl);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER , false);
            
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($data_string))
            );
            
            $result = curl_exec($ch);
            $result=  json_decode($result);
            curl_close($ch);
            if(isset($result->secure_token)){
                $sToken = $result->secure_token;
                $trnsID = $ekp_array["trns_info"]["trnx_id"];
                return response([
                    'success' => true,
                    'message' => 'Token found success',
                    'url'    => $this->domain.$sToken.'&trnsID='.$trnsID
                ]);

            } else {
                return response([
                    'success' => false,
                    'message' => 'Ekpay url not found.Please click Bypass payment.',
                    'url'    => 'https://ekpay.gov.bd/'
                ]);
            }

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'token not found.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }
    }



    // ******************** Water Testing, Smart Card, Pump Operator Payment  Success Payment Start*****************

    public function defaultSuccess($trnsId){
        if(!empty($trnsId)){
            $irrigation_payment = IrrigationPayment::where('transaction_no', $trnsId)->first();
            if ($irrigation_payment && $irrigation_payment->status == 1) {

                DB::beginTransaction();

                $irrigation_payment->status     = 2;
                $irrigation_payment->pay_status = 'success';
                $irrigation_payment->update();


                $this->pumpOptPaymentSuccess($irrigation_payment);
                $this->smartCardPaymentSuccess($irrigation_payment);
                $this->waterTestingPaymentSuccess($irrigation_payment);

                DB::commit();
                return response([
                    'success' => 2,
                    'message' => 'Payment paid successfully.'
                ]);
            } else {
                DB::rollback();
                return response([
                    'success' => false,
                    'message' => 'Invalid Transaction Number.'
                ]);
            }
        }
    }

    private function pumpOptPaymentSuccess ($irrigation_payment) {
        if ($irrigation_payment->application_type == 2) {                    
            $FarmerWaterTestApplication = FarmerPumpOperatorApplication::find($irrigation_payment->far_application_id);
            // form fee payment
            if ($irrigation_payment->payment_type_id == 1) {
                $FarmerWaterTestApplication->status = 2; // 2 mean processing
                $FarmerWaterTestApplication->payment_status = 1;
                $FarmerWaterTestApplication->save();
            }
            // security fee payment
            if ($irrigation_payment->payment_type_id == 3) {
                $FarmerWaterTestApplication->payment_status = 2;
                $FarmerWaterTestApplication->save();
            }
             // renew fee payment
            if ($irrigation_payment->payment_type_id == 2) {
                $renew_app = FarmerPumpOperatorApplicationReniews::where('pump_opt_apps_id', $FarmerWaterTestApplication->id)->first();
                $renew_app->payment_status = 1;
                $renew_app->save();

                $FarmerWaterTestApplication->status = 2; // 2 mean processing
                $FarmerWaterTestApplication->save();
            }
         }
    }

    private function smartCardPaymentSuccess ($irrigation_payment) {
        if ($irrigation_payment->application_type == 3) {

            $farSmartCard = FarmerSmartCardApplication::find($irrigation_payment->far_application_id);
            $farSmartCard->payment_status = 1;
            $farSmartCard->save();

            if ($irrigation_payment->payment_type_id === 2) {
               $farSmartCard->reissue_status = 2;
               $farSmartCard->save();
            }
         }
    }

    private function waterTestingPaymentSuccess ($irrigation_payment) {
        if ($irrigation_payment->application_type == 4) {
            $FarmerWaterTestApplication = FarmerWaterTestApplication::find($irrigation_payment->far_application_id);
            $FarmerWaterTestApplication->payment_status = 1;
            $FarmerWaterTestApplication->save();
         }
    }


    // ******************** Water Testing, Smart Card, Pump Operator Payment  Success Payment End*****************



    // ******************** BADC  Success Payment Start*****************

    public function defaultSuccessBadc($trnsId){
        if(!empty($trnsId)){
            $irrigation_payment = IrrigationPayment::where('transaction_no', $trnsId)->first();
            if ($irrigation_payment && $irrigation_payment->status == 1) {
                DB::commit();
                try {
                    // irrigation payment table status update
                    $irrigation_payment->status     = 2;
                    $irrigation_payment->pay_status = 'success';
                    $irrigation_payment->update();
                    

                    $this->formFeeSuccessOtherOrg($irrigation_payment);
                    $this->BadcParticipationFeeSuccess($irrigation_payment);
                    $this->BadcSecurityMoneySuccess($irrigation_payment);


                    DB::commit();
                    return response([
                        'success' => 2,
                        'message' => 'Payment paid successfully.'
                    ]);
                } catch (\Exception $ex) {
                    DB::rollback();
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

    // scheme application form fee other org and badc bmda payment
    private function formFeeSuccessOtherOrg ($irrigation_payment) {
        if ($irrigation_payment->payment_type_id == 1 || $irrigation_payment->payment_type_id == 0) {
            $FarSchApplication = FarmerSchemeApplication::find($irrigation_payment->far_application_id);
            $FarSchApplication->payment_status = 1;
            $FarSchApplication->save();
        }
    }

    // scheme application participation fee and participation fee due payment
    private function BadcParticipationFeeSuccess ($irrigation_payment) {
        if ($irrigation_payment->payment_type_id == 2) {
            $scheme_part_fees = json_decode($irrigation_payment->scheme_participation_fee_id);
            foreach ($scheme_part_fees as $fee) {
                $scheme_part_fee_single = SchemeParticipationFee::find($fee->id);
                $scheme_part_fee_single->payment_status = 2;
                $scheme_part_fee_single->payment_date = date('Y-m-d');
                $scheme_part_fee_single->update();
            }
            if(1 > $this->advancePayCheck($irrigation_payment)) {
                $this->BadcAppStatusUpdateAfterParticipationFee($irrigation_payment->far_application_id);
            }
        }
    }

    // scheme application security money and due payment
    private function BadcSecurityMoneySuccess ($irrigation_payment) {
        if ($irrigation_payment->payment_type_id == 3) {
            $scheme_security_fees = json_decode($irrigation_payment->scheme_security_money_id);
            foreach ($scheme_security_fees as $fee) {
                $scheme_security_fee_single = SchemeSecurityMoney::find($fee->id);
                $scheme_security_fee_single->payment_status = 2;
                $scheme_security_fee_single->payment_date = date('Y-m-d');
                $scheme_security_fee_single->update();
            }
        }
        if(1 > $this->advancePayCheck($irrigation_payment)) {
            $this->BadcAppStatusUpdateAfterParticipationFee($irrigation_payment->far_application_id);
        }    
    }

    // scheme application security money and due payment
    private function BadcAppStatusUpdateAfterParticipationFee ($id) {
        $FarSchApplication = FarmerSchemeApplication::find($id);
        if($FarSchApplication && $FarSchApplication->status === 12) {
            $FarSchApplication->status = 8;
            $FarSchApplication->save();
        }
    }

    // is paid check security or participation fee
    private function advancePayCheck ($irrigation_payment) {
        $total_part_advance = SchemeParticipationFee::where('scheme_application_id', $irrigation_payment->far_application_id)->where('payment_status',1)->where('payment_type',1)->count();
        $total_security_advance = SchemeSecurityMoney::where('scheme_application_id', $irrigation_payment->far_application_id)->where('payment_status',1)->where('payment_type',1)->count();
        return $total_part_advance + $total_security_advance;
    }


    // ******************** BADC  Success Payment Closed *****************






    // ******************** BMDA  Success Payment Start*****************

    public function defaultSuccessBmda($trnsId){
        if(!empty($trnsId)){

            $irrigation_payment = IrrigationPayment::where('transaction_no', $trnsId)->first();
            if ($irrigation_payment && $irrigation_payment->status == 1) {
                DB::commit();

                try {
                    $irrigation_payment->status     = 2;
                    $irrigation_payment->pay_status = 'success';
                    $irrigation_payment->update();
    
                    $this->formFeeSuccessBmda($irrigation_payment);
                    $this->BmdaParticipationFeeSuccess($irrigation_payment);

                    DB::commit();
                    return response([
                        'success' => 2,
                        'message' => 'Payment paid successfully.'
                    ]);
                } catch (\Exception $ex) {
                    DB::rollback();
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

    // scheme application form fee other org and badc bmda payment
    private function formFeeSuccessBmda ($irrigation_payment) {
        if ($irrigation_payment->payment_type_id == 1) {
            $FarSchApplication = FarmerSchemeApplication::find($irrigation_payment->far_application_id);
            $FarSchApplication->payment_status = 1;
            $FarSchApplication->save();
        }
    }

    // scheme application participation fee and participation fee due payment
    private function BmdaParticipationFeeSuccess ($irrigation_payment) {
        if ($irrigation_payment->payment_type_id == 2) {
            $scheme_part_fee_single = SchemeParticipationFee::find($irrigation_payment->scheme_participation_fee_id);
            $scheme_part_fee_single->payment_status = 2;
            $scheme_part_fee_single->payment_date = date('Y-m-d');
            $scheme_part_fee_single->update();
            $this->BmdaAppStatusUpdateAfterParticipationFee($irrigation_payment->far_application_id);
        }
    }

     // scheme application security money and due payment
     private function BmdaAppStatusUpdateAfterParticipationFee ($id) {
        $FarSchApplication = FarmerSchemeApplication::find($id);
        if($FarSchApplication && $FarSchApplication->status === 12) {
            $FarSchApplication->status = 8;
            $FarSchApplication->save();
        }
    }

    // ******************** BADC  Success Payment Closed *****************


}