<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\FarRefundDeduct;
use App\Models\Payment\IrrigationPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Validations\Payment\FarRefundValidation;

class FarRefundController extends Controller
{
    public function index(Request $request)
    {
        /*$query = DB::table('irrigation_payments')
        ->leftJoin('far_basic_infos', 'irrigation_payments.farmer_id', '=', 'far_basic_infos.farmer_id')
        ->where('irrigation_payments.pay_status', 'success')
        ->select(
            'irrigation_payments.id as irri_payment_id',
            'irrigation_payments.*',
            'far_basic_infos.id as user_id',
            'far_basic_infos.*'
        )->orderBy('irrigation_payments.created_at', 'desc')->chunk(100, function ($payments) {
            foreach ($payments as $payment) {
                $latestEntry = DB::table('far_app_payment_refunds_deducts')->where('payment_id', $payment->irri_payment_id)->orderBy('id', 'desc')->first();
                $payment->present_balance = $latestEntry->present_balance;
            }
        });*/

        $query = IrrigationPayment::with('farmerBasicInfos')
            ->with(['refundDeducts' => function ($q) {
                $q->orderBy('created_at', 'desc');//->first();
            }])
            ->where('pay_status', 'success')
            ->orderBy('created_at', 'desc');

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->far_application_id) {
            $query = $query->where('far_application_id', $request->far_application_id);
        }

        if ($request->application_type) {
            $query = $query->where('application_type', $request->application_type);
        }

        if ($request->far_application_id) {
            $query = $query->where('far_application_id', $request->far_application_id);
        }

        if ($request->payment_type_id) {
            $query = $query->where('payment_type_id', $request->payment_type_id);
        }

        if ($request->division_id) {
            $query = $query->whereHas('farmerBasicInfos', function ($q) use($request) {
                $q->where('far_division_id', $request->division_id);
            });
        }
        if ($request->district_id) {
            $query = $query->whereHas('farmerBasicInfos', function ($q) use($request) {
                $q->where('far_district_id', $request->district_id);
            });
        }
        if ($request->upazilla_id) {
            $query = $query->whereHas('farmerBasicInfos', function ($q) use($request) {
                $q->where('far_upazilla_id', $request->upazilla_id);
            });
        }
        if ($request->union_id) {
            $query = $query->whereHas('farmerBasicInfos', function ($q) use($request) {
                $q->where('far_union_id', $request->union_id);
            });
        }

        $list =  $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Farmer Refund Application List',
            'data' => $list,
            'test' => $request->irri_payment_id
        ]);
    }

    /* Required fields from frontend:
    *  payment_id, mobile_no, refund_amount, user_name(Name),
     * deduction_amount, refund_by (Bkash/Rocket/Bank/Cash))
     * account_no, reason, operation_type (1 = Refund, 2 = Deduct)
     * operation_date
    */
    public function store (Request $request) {
        $isRefund               = (int)$request->operation_type === 1; // Refund
        $isDeduct               = (int)$request->operation_type === 2; // Deduct

        $validationResult = FarRefundValidation::validate($request, $isRefund, $isDeduct);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $existingRefundDeduct   = FarRefundDeduct::where('payment_id', $request->payment_id);
        $latestRefundDeduct     = FarRefundDeduct::where('payment_id', $request->payment_id)->latest()->first();
        $commonPayment          = IrrigationPayment::find($request->payment_id);

        $previouslyDeducted     = 0;
        $previouslyRefunded     = 0;
        $refundedTotal          = 0;
        $deductTotal            = 0;
        $amountPaid             = $commonPayment->amount;

        if ($latestRefundDeduct) {
            if ($latestRefundDeduct->present_balance < $request->refund_amount || !$latestRefundDeduct->present_balance) {
                return ([
                    'success' => false,
                    'errors' => 'Low balance, refund amount cannot be more than present balance.'
                ]);
            }

            if ($latestRefundDeduct->present_balance < $request->deduction_amount || !$latestRefundDeduct->present_balance) {
                return ([
                    'success' => false,
                    'errors' => 'Low balance, deduction amount cannot be more than present balance.'
                ]);
            }

            if ($request->deduction_amount && $request->deduction_amount <= 0 || $request->refund_amount && $request->refund_amount <= 0) {
                return ([
                    'success' => false,
                    'errors' => 'Minimum amount is 1.'
                ]);
            }
        }

        if ($existingRefundDeduct->exists()){
            $refundedTotal          = $existingRefundDeduct->sum('refund_amount');
            $deductTotal            = $existingRefundDeduct->sum('deduction_amount');
            $previouslyDeducted     = $latestRefundDeduct->deduction_amount;
            $previouslyRefunded     = $latestRefundDeduct->refund_amount;
        }
        try {
                DB::beginTransaction();

                $presentBalance                  = ($latestRefundDeduct ? ($latestRefundDeduct->present_balance ?? $amountPaid) : $amountPaid) - (float)($request->refund_amount ?? 0) - (float)($request->deduction_amount ?? 0);
                $model                           = new FarRefundDeduct();
                $model->org_id                   = $commonPayment->org_id;
                $model->application_id           = $commonPayment->far_application_id;
                $model->application_type_id      = $commonPayment->application_type;
                $model->payment_type_id          = $commonPayment->payment_type_id;
                $model->amount_paid              = (float)$amountPaid;
                $model->present_balance          = $presentBalance;
                $model->payment_id               = $request->payment_id;
                $model->master_payment_id        = $commonPayment->master_payment_id;
                $model->farmer_id                = $commonPayment->farmer_id;
                $model->mobile_no                = $request->mobile_no;

               if ($isRefund) {
                   $model->refund_amount            = (float)$request->refund_amount;
                   $model->refund_balance           = (float)$refundedTotal + (float)$request->refund_amount;
                   $model->previously_refunded      = $previouslyRefunded;
                   $model->user_name                = $request->user_name;
                   $model->refund_by                = $request->refund_by;
               }

                if ($isDeduct) {
                    $model->previously_deducted      = $previouslyDeducted;
                    $model->deduction_amount         = (float)$request->deduction_amount;
                    $model->balance_after_deduction  = $deductTotal + (float)$request->deduction_amount;
//                    $model->deduction_balance        = $deductTotal + (float)$request->deduction_amount;
                }

               $model->account_no               = $request->account_no;
               $model->reason                   = $request->reason;
               $model->operation_type           = (int)$request->operation_type;
               $model->operation_date           = $request->operation_date;
               $model->created_by               = (int)user_id();
               $model->updated_by               = (int)user_id();
                $model->save();
               /* if ($model->save()) {
                   FarRefundDeduct::where('farmer_id', $commonPayment->farmer_id)->update(['present_balance' => $presentBalance]);
                   IrrigationPayment::where('farmer_id', $commonPayment->farmer_id)
                       ->where('farmer_id', $commonPayment->farmer_id)
                       ->update(['present_balance' => $presentBalance]);
               } */

               save_log([
                   'data_id' => $model->id,
                   'table_name' => 'far_app_payment_refunds_deducts',
               ]);

                DB::commit();

                return response([
                    'success' => true,
                    'message' => 'Data saved successfully',
                    'data'    => $model
                ]);
           } catch (\Exception $ex) {

                DB::rollBack();

               return response([
                   'success' => false,
                   'message' => 'Failed to save data.',
                   'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ''
               ]);
           }
    }
}
