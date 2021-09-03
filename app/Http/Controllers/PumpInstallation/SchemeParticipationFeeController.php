<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Models\Config\MasterPayment;
use App\Models\PumpInstallation\SchemeParticipationFee;
use App\Models\PumpInstallation\SchemeSecurityMoney;
use Illuminate\Http\Request;
use DB;

class SchemeParticipationFeeController extends Controller
{
    /**
     * add fee
     */
    public function store(Request $request)
    {  
        DB::beginTransaction();

        try {
            $data = $request->all();
            if(isset($data['circle_area_id'])) {
                SchemeParticipationFee::where('scheme_application_id', $request->scheme_application_id)->delete();
                SchemeParticipationFee::create($data);
            }
            if(isset($request->participationFee)){
                SchemeParticipationFee::where('scheme_application_id', $request->scheme_application_id)->delete();
                foreach($request->participationFee as $parFee) {
                    $participationFee                               = new SchemeParticipationFee();
                    $participationFee->scheme_application_id        = $request->scheme_application_id;
                    $participationFee->org_id                       = $request->org_id;
                    $participationFee->participation_category_id    = $parFee['participation_category_id'];
                    $participationFee->circle_area_id               = $parFee['circle_area_id'] ?? null;
                    $participationFee->discharge_cusec              = $parFee['discharge_cusec'];
                    $participationFee->amount                       = $parFee['amount'];
                    $participationFee->payment_type                 = $parFee['payment_type'];
                    $participationFee->payment_status                 = $parFee['payment_status'];
                    $participationFee->save();
                }
            }
            if(isset($request->securityMoney) && count($request->securityMoney) > 0 && $request->securityMoney[0]['pump_type_id'] != 0){
                SchemeSecurityMoney::where('scheme_application_id', $request->scheme_application_id)->delete();
                foreach($request->securityMoney as $secFee) {
                    $securityFee                        = new SchemeSecurityMoney();
                    $securityFee->scheme_application_id = $request->scheme_application_id;
                    $securityFee->org_id                = $request->org_id;
                    $securityFee->pump_type_id          = $secFee['pump_type_id'];
                    $securityFee->discharge_cusec       = $secFee['discharge_cusec'];
                    $securityFee->amount                = $secFee['amount'];
                    $securityFee->payment_type          = $secFee['payment_type'];
                    $securityFee->payment_status        = $secFee['payment_status'];
                    $securityFee->save();
                }
            }
            DB::commit();

        } catch (\Exception $ex) {

            DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => []
        ]);

    }

    /**
     * fee details
     */
    public function details($scheme_application_id, $org_id)
    {
        $participationFee = SchemeParticipationFee::select(
                'participation_category_id','discharge_cusec as id',
                'discharge_cusec as text','discharge_cusec', 'amount','circle_area_id',
                'payment_status' ,'payment_date','payment_type'
            )
            ->where('scheme_application_id', $scheme_application_id)
            ->get()->each(function ($participation) use($org_id) {
                $participation->participationCusecList = MasterPayment::select('id','discharge_cusec as value','discharge_cusec as text','amount')
                ->where('participation_category_id', $participation->participation_category_id)
                ->where('org_id', $org_id)
                ->where('participation_category_id', '!=', 0)
                ->get();
            });
        $securityFee = SchemeSecurityMoney::select(
            'pump_type_id','discharge_cusec as id',
            'discharge_cusec as text','discharge_cusec', 
            'amount','payment_type',
            'payment_status' ,'payment_date'
            )
            ->where('scheme_application_id', $scheme_application_id)
            ->get()->each(function ($securityFee) use($org_id) {
                $securityFee->securityMoneyCusecList = MasterPayment::select(
                    'id','discharge_cusec as value','discharge_cusec as text','amount'
                )
                ->where('pump_type_id', $securityFee->pump_type_id)
                ->where('pump_type_id', '!=', 0)
                ->where('org_id', $org_id)
                ->get();
            });

        if ($participationFee->count() > 0 || $securityFee->count() > 0 ) {
            return response([
                'success' => true,
                'data'    => [
                    'participationFee'  => $participationFee,
                    'securityFee'       => $securityFee
                ]
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
    }

    /**
     * get participation cusec list
     */
    public function participationCusec(Request $request)
    {
        $participationCusec = MasterPayment::select('id','discharge_cusec as value','discharge_cusec as text','amount')
            ->where('participation_category_id', $request->participation_category_id)
            ->where('participation_category_id', '!=', 0)
            ->where('org_id', $request->org_id)
            ->get();

        if ($participationCusec->count() > 0) {
            return response([
                'success' => true,
                'data'    => $participationCusec
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
    }
    public function participationCircle(Request $request,$orgId,$scheme_application_id)
    { 
        $datas = $request->all();
        $new_data= array();
        if(!empty($datas)) {
            foreach($datas as $key=>$value) {
                $masterpayment = MasterPayment::select('id','org_id','circle_area_id','amount')
                    ->where('circle_area_id', $value['value'])
                    ->where('org_id', $orgId)
                    ->first();
                if(!empty($masterpayment)){
                    $value['amount'] =$masterpayment->amount;
                    $new_data[] = $value;
                }
            }
        }
        
        $participationFee   = SchemeParticipationFee::where('org_id',$orgId)->where('scheme_application_id', $scheme_application_id)->first();
        
        return response([
            'success' => true,
            'data'    => array(
                'datas' =>$new_data,
                'participationFee' =>$participationFee,
            )
        ]);
    }
    /**
     * get security money cusec list
     */
    public function securityMoneyCusec(Request $request)
    {
        $securityMoneyCusec = MasterPayment::select('id','discharge_cusec as value','discharge_cusec as text','amount')
            ->where('pump_type_id', $request->pump_type_id)
            ->where('pump_type_id', '!=', 0)
            ->where('org_id', $request->org_id)
            ->get();

        if ($securityMoneyCusec->count() > 0) {
            return response([
                'success' => true,
                'data'    => $securityMoneyCusec
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }
    }
}
