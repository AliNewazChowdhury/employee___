<?php

namespace App\Http\Controllers\Getpayment;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Config\MasterPayment;
use App\Models\PumpInstallation\SchemeParticipationFee;

class GetBmdaPaymentController extends Controller
{
    public function schemeApplicationPaymentFromFee(Request $request) {
        $scheme_type_id = $request->scheme_type_id;
        if(!empty($scheme_type_id)) {
            $payment = MasterPayment::select(['id','amount'])->where('org_id', 15)->where('application_type_id', 1)->where('scheme_type_id', $scheme_type_id)->where('payment_type_id', 1)->first();

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
    public function schemeApplicationPaymentPartFee(Request $request) {
        $scheme_application_id = $request->scheme_application_id;
        if(!empty($scheme_application_id)) {
            $payments = SchemeParticipationFee::select('id','scheme_application_id','participation_category_id','discharge_cusec','participation_category_id','payment_type','payment_date','amount','payment_status','circle_area_id')->where('org_id', 15)->where('scheme_application_id', $scheme_application_id)->get();
            
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
}
