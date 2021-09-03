<?php
namespace App\Http\Validations\Payment;

use Illuminate\Support\Facades\Validator;

class FarRefundValidation
{
    public static function validate ($request, $isRefund, $isDeduct): array
    {

        $rules = [
            'operation_type'        => 'required',
            'payment_id'            => 'required',
//            'mobile_no'             => 'required',
            'operation_date'        => 'required',
            'account_no'            => 'required',
            'reason'                => 'required|max:255',
            'user_name'             => 'required|max:255'
        ];

        $refundRules = [
            'refund_amount'         => 'required|min:0',
            'refund_by'             => 'required' // Bkash/Rocket/Bank/Cash
        ];

        $deductRules = [
            'deduction_amount'      => 'required|min:0'
        ];

        if (!$isRefund && !$isDeduct) {
            return ([
                'success' => false,
                'errors' => 'Please specify a valid operation type.'
            ]);
        }

        $validationRules = array_merge($rules, $isRefund ? $refundRules : ($isDeduct ? $deductRules : []));

        $validator = Validator::make($request->all(), $validationRules);

        if ($validator->fails()) {
            return ([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        return ['success'=> 'true'];
    }
}
