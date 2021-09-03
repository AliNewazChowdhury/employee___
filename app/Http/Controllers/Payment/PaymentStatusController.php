<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;
use App\Models\Payment\IrrigationPayment;
use App\Models\PumpInstallation\SchemeSecurityMoney;
use App\Models\SmartCard\FarmerSmartCardApplication;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\SchemeParticipationFee;
use App\Models\WaterTesting\FarmerWaterTestApplication;
use App\Models\PumpOperator\FarmerPumpOperatorApplication;

class PaymentStatusController extends Controller
{
    // ******************** Water Testing, Smart Card, Pump Operator Payment  Success Payment Start*****************

    public function Success(Request $request){
        $trnsId=	$request->transId;
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
                    'success' => true,
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
             if ($irrigation_payment->payment_type_id == 1) {
                 $payment_status = 1;
                 $FarmerWaterTestApplication->status = 2; // 2 mean processing
             } else {
                 $payment_status =2;
             }
            $FarmerWaterTestApplication->payment_status = $payment_status;
            $FarmerWaterTestApplication->save();
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

    public function SuccessBadc(Request $request){
        $trnsId=	$request->transId;
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
                        'success' => true,
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

    public function SuccessBmda(Request $request){
        $trnsId=	$request->transId;
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
                        'success' => true,
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


    // ******************** success decline Payment start *****************
    public function decline(Request $request){
        $trnsId=	$request->transId;
        if(!empty($trnsId)){
            $irrigation_payment = IrrigationPayment::where('transaction_no', $trnsId)->first();
            if ($irrigation_payment && $irrigation_payment->status == 1) {
                $irrigation_payment->status     = 2;
                $irrigation_payment->update();
                return response([
                    'success' => true,
                    'message' => 'Payment failed.'
                ]);
            } else {
                return response([
                    'success' => false,
                    'message' => 'Invalid Transaction Number.'
                ]);
            }
        }
    }

    public function cancel(Request $request){
        $trnsId=	$request->transId;
        if(!empty($trnsId)){
            $irrigation_payment = IrrigationPayment::where('transaction_no', $trnsId)->first();
            if ($irrigation_payment && $irrigation_payment->status == 1) {
                $irrigation_payment->status     = 2;
                $irrigation_payment->update();
                return response([
                    'success' => true,
                    'message' => 'Payment cancel successfully.'
                ]);
            } else {
                return response([
                    'success' => false,
                    'message' => 'Invalid Transaction Number.'
                ]);
            }
        }
    }
    // ******************** success decline Payment Closed *****************



}
