<?php

namespace App\Http\Controllers\GetPayment;

use DB;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Config\MasterPayment;
use App\Models\Payment\IrrigationPayment;
use App\Models\Payment\IrriSchemePayment;
use App\Models\PumpInstallation\SchemeSecurityMoney;
use App\Models\SmartCard\FarmerSmartCardApplication;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\SchemeParticipationFee;

use App\Models\WaterTesting\FarmerWaterTestApplication;
use App\Models\PumpOperator\FarmerPumpOperatorApplication;

class GetAllPaymentController extends Controller
{
    // get water testing payment here
    public function waterTestionPayment(Request $request) {
        if(!empty($request->org_id) && !empty($request->testing_type_id) && !empty($request->testing_parameter_id)) {
            $org_id = $request->org_id;
            $testing_type_id = $request->testing_type_id;
            $testing_parameter_id = $request->testing_parameter_id;
            
            $data = [];
            $total_amount = 0;
            try {
                foreach ($testing_parameter_id as $val) {
                    $temp = json_decode($val);
                    $amount = $this->getWaterAmount($org_id, $testing_type_id, $temp->value);
                    if ($amount) {
                        $amount = $amount;
                    } else {
                        $amount = 0;
                    }
                    $temp->amount= $amount;
                    array_push($data, $temp);
                    $total_amount += $amount;
                }

                return response()->json([
                    "success" =>true,
                    "message" => "Payment found.",
                    "data"    => $data,
                    "total_amount"    => $total_amount
                ]);
            } catch (Exception $e) {
                return response()->json([
                    "success" =>false,
                    "message" => "Payment not found.",
                    "data"    => [],
                    "total_amount" => 0
                ]);
            }
        } else {
            return response()->json([
            "success" =>false,
            "message" => "Payment not found.",
            "data"    => [],
            "total_amount" => 0
        ]);
    }
        
     
    }

    private function getWaterAmount ($org_id = 0, $testing_type_id=0, $testing_parameter_id=0) {
        if(!empty($org_id) && !empty($testing_type_id) && !empty($testing_parameter_id)) {
            $payment = MasterPayment::select(['id','amount'])->where('org_id', $org_id)->where('application_type_id',4)->where('payment_type_id',$testing_type_id)->where('testing_parameter_id',$testing_parameter_id)->first();
            if ($payment) {
                return $payment->amount;
            } else {
               return false;
            }
        } else {
            return false;
        }
    }


    // get smart card payment here
    public function smartCardPayment(Request $request) {
        $org_id = $request->org_id;
        $payment_type_id = $request->payment_type_id;

        if(!empty($org_id) && !empty($payment_type_id)) {
            $payment = MasterPayment::select(['id','amount'])->where('org_id', $org_id)->where('application_type_id',3)->where('payment_type_id',$payment_type_id)->first();

            if ($payment) {
                return response([
                    "success" =>true,
                    "message" => "Payment found",
                    "data"    =>$payment
                ]);
            } else {
                return response([
                    "success" =>false,
                    "message" => "Payment not found.",
                    "data"    => []
                ]);
            }
        } else {
            return response([
                "success" =>false,
                "message" => "Validation error"
            ]);
        }
    }
    // get pump opt payment here
    public function pumpOperatorPayment(Request $request) {
        $org_id = $request->org_id;
        $payment_type_id = $request->payment_type_id;

        if(!empty($org_id) && !empty($payment_type_id)) {

            if ($request->payment_type_id == 3) {
                $payment = MasterPayment::select(['id','amount'])->where('org_id', $org_id)->where('application_type_id',2)->where('payment_type_id',$payment_type_id)->where('gender',$request->gender)->first();
            } else {
                $payment = MasterPayment::select(['id','amount'])->where('org_id', $org_id)->where('application_type_id',2)->where('payment_type_id',$payment_type_id)->first();
            }

            if ($payment) {
                return response([
                    "success" =>true,
                    "message" => "Payment found",
                    "data"    =>$payment
                ]);
            } else {
                return response([
                    "success" =>false,
                    "message" => "Payment not found.",
                    "data"    => []
                ]);
            }
        } else {
            return response([
                "success" =>false,
                "message" => "Validation error"
            ]);
        }
    }
    public function schemeApplicationPayment(Request $request) {
        $org_id = $request->org_id;

        if(!empty($org_id)) {
            $payment = MasterPayment::select(['id','amount'])->where('org_id', $org_id)->where('application_type_id',1)->first();

            if ($payment) {
                return response([
                    "success" =>true,
                    "message" => "Payment found",
                    "data"    =>$payment
                ]);
            } else {
                return response([
                    "success" =>false,
                    "message" => "Payment not found.",
                    "data"    => []
                ]);
            }
        } else {
            return response([
                "success" =>false,
                "message" => "Validation error"
            ]);
        }
    }
    public function schemeApplicationPaymentBadcFromFee(Request $request) {
        $scheme_type_id = $request->scheme_type_id;
        if(!empty($scheme_type_id)) {
            $payment = MasterPayment::select(['id','amount'])->where('org_id', 3)->where('application_type_id',1)->where('scheme_type_id', $scheme_type_id)->where('payment_type_id',1)->first();

            if ($payment) {
                return response([
                    "success" =>true,
                    "message" => "Payment found",
                    "data"    =>$payment
                ]);
            } else {
                return response([
                    "success" =>false,
                    "message" => "Payment not found.",
                    "data"    => []
                ]);
            }
        } else {
            return response([
                "success" =>false,
                "message" => "Validation error"
            ]);
        }
    }
    public function schemeApplicationOtherApplicationFee(Request $request) {
        $org_id = $request->org_id;
        if(!empty($org_id)) {
            $payment = MasterPayment::select(['id','amount'])
                                    ->where('org_id', $request->org_id)
                                    ->where('application_type_id',1)
                                    ->where('payment_type_id', 0)
                                    ->where('scheme_type_id', 0)->first();

            if ($payment) {
                return response([
                    "success" =>true,
                    "message" => "Payment found",
                    "data"    =>$payment
                ]);
            } else {
                return response([
                    "success" =>false,
                    "message" => "Payment not found.",
                    "data"    => []
                ]);
            }
        } else {
            return response([
                "success" =>false,
                "message" => "Validation error"
            ]);
        }
    }
    public function schemeApplicationPaymentBadcPartFee(Request $request) {
        $scheme_application_id = $request->scheme_application_id;
        if(!empty($scheme_application_id)) {
            $payments = SchemeParticipationFee::select('id','scheme_application_id','participation_category_id','discharge_cusec','participation_category_id','payment_type','payment_date','amount','payment_status')->where('org_id', 3)->where('scheme_application_id', $scheme_application_id)->get();
            
            if ($payments) {
                return response([
                    "success" =>true,
                    "message" => "Payment found",
                    "data"    =>$payments
                ]);
            } else {
                return response([
                    "success" =>false,
                    "message" => "Payment not found.",
                    "data"    => []
                ]);
            }
        } else {
            return response([
                "success" =>false,
                "message" => "Validation error"
            ]);
        }
    }
    public function schemeApplicationPaymentBadcSecurityFee(Request $request) {
        $scheme_application_id = $request->scheme_application_id;
        if(!empty($scheme_application_id)) {
            $payments = SchemeSecurityMoney::select('id','scheme_application_id','pump_type_id','discharge_cusec','amount','payment_status','payment_type')->where('org_id', 3)->where('scheme_application_id', $scheme_application_id)->get();
            
            if ($payments) {
                return response([
                    "success" =>true,
                    "message" => "Payment found",
                    "data"    =>$payments
                ]);
            } else {
                return response([
                    "success" =>false,
                    "message" => "Payment not found.",
                    "data"    => []
                ]);
            }
        } else {
            return response([
                "success" =>false,
                "message" => "Validation error"
            ]);
        }
    }
    
    public function success(Request $request){
        $trnsId=	$request->transId;
        if(!empty($trnsId)){
            $irrigation_payment = IrrigationPayment::where('transaction_no', $trnsId)->first();
            if ($irrigation_payment && $irrigation_payment->status == 1) {

                DB::beginTransaction();

                $irrigation_payment->status     = 2;
                $irrigation_payment->pay_status = 'success';
                $irrigation_payment->update();

                if ($irrigation_payment->application_type == 4) {
                   $FarmerWaterTestApplication = FarmerWaterTestApplication::find($irrigation_payment->far_application_id);
                   $FarmerWaterTestApplication->payment_status = 1;
                   $FarmerWaterTestApplication->save();
                }
                
                if ($irrigation_payment->application_type == 3) {
                    $farSmartCard = FarmerSmartCardApplication::find($irrigation_payment->far_application_id);
                    $farSmartCard->payment_status = 1;
                    $farSmartCard->save();
                }

                if ($irrigation_payment->application_type == 2) {
                   $FarmerWaterTestApplication = FarmerPumpOperatorApplication::find($irrigation_payment->far_application_id);
                   $FarmerWaterTestApplication->status         = 2;
                   $FarmerWaterTestApplication->payment_status = 1;
                   $FarmerWaterTestApplication->save();
                }

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

    public function successScheme(Request $request){
        $trnsId=	$request->transId;
        if(!empty($trnsId)){
            $irrigation_payment = IrrigationPayment::where('transaction_no', $trnsId)->first();
            if ($irrigation_payment && $irrigation_payment->status == 1) {

                try {
                    $irrigation_payment->status     = 2;
                    $irrigation_payment->pay_status = 'success';
                    $irrigation_payment->update();
                    
                    if ($irrigation_payment->payment_type_id == 1 || $irrigation_payment->payment_type_id == 0) {
                        $FarSchApplication = FarmerSchemeApplication::find($irrigation_payment->far_application_id);
                        $FarSchApplication->payment_status = 1;
                        $FarSchApplication->save();
                    }
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
                        'success' => 2,
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
    public function declineScheme(Request $request){
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
    public function cancelScheme(Request $request){
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

}
