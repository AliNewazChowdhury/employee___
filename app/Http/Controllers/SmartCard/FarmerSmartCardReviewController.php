<?php

namespace App\Http\Controllers\SmartCard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SmartCard\FarmerSmartCardApplication;
use App\Models\SmartCard\FarmerSmartCardReview;
use App\Http\Validations\SmartCard\FarmerSmartCardReviewValidations;
use DB;

class FarmerSmartCardReviewController extends Controller
{
	public function __construct()
    {
      // 
    }

    public function updateAsIssued(Request $request)
    {
       $validationResult = FarmerSmartCardReviewValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }       

        DB::beginTransaction();
        try {
            $id = $request->far_smart_card_apps_id;
            $smartCardReview = FarmerSmartCardApplication::find($id);
            $smartCardReview->status = 7;
            
            if($smartCardReview->update()){
            	$farSmartCardReview        					 = new FarmerSmartCardReview();
    			$farSmartCardReview->far_smart_card_apps_id  = (int)$id;
    			$farSmartCardReview->note  					 = $request->note??null;
    			$farSmartCardReview->note_bn  				 = $request->note_bn??null;
    			$farSmartCardReview->save();
            }

            DB::commit();
            save_log([
                'data_id' => $farSmartCardReview->id,
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
            'message' => 'Data save successfully',
            'data'    => $farSmartCardReview
        ]);
    }

       

}
