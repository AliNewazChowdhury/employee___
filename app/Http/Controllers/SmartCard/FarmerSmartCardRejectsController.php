<?php

namespace App\Http\Controllers\SmartCard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmartCard\FarmerSmartCardApplication;
use App\Models\SmartCard\FarmerSmartCardRejects;
use App\Http\Validations\SmartCard\FarmerSmartCardRejectsValidations;
use App\Library\SmsLibrary;
use DB;

class FarmerSmartCardRejectsController extends Controller
{
    public function __construct()
    {
      // 
    }

    public function updateAsReject(Request $request)
    {   
        $validationResult = FarmerSmartCardRejectsValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();

        try {
            $id = $request->far_smart_card_apps_id;
            $smartCardReject = FarmerSmartCardApplication::find($id);
            $smartCardReject->status = 6;

            if($smartCardReject->update()){
            	$farSmartCardRejects       					  	= new FarmerSmartCardRejects();
    			$farSmartCardRejects->far_smart_card_apps_id  	= (int)$request->far_smart_card_apps_id;
    			$farSmartCardRejects->reject_note         		= $request->reject_note;
    			$farSmartCardRejects->reject_note_bn         	= $request->reject_note_bn ?? null;           
                $farSmartCardRejects->save();
            }

            if ($request->supervisor_phone != null) {
                $smsData['mobile']  = $request->supervisor_phone;
                $smsData['message'] = "Smart Card Application (ID :". $smartCardReject->application_id . ') is rejected';
                $sms = new SmsLibrary();
                $sms->sms_helper($smsData); 
            }    

            DB::commit();

            save_log([
                'data_id' => $smartCardReject->id,
                'table_name' => 'far_smart_card_apps',
            ]);

        } catch (\Exception $ex) {
        	DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Application reject save successfully',
            'data'    => $smartCardReject
        ]);
    }

    
}
