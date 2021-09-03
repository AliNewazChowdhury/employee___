<?php

namespace App\Http\Controllers\PumpOperator;

use App\Http\Controllers\Controller;
use App\Models\PumpOperator\FarmerPumpOperatorApplicationReniews;
use App\Models\PumpOperator\FarmerPumpOperatorReniewsReject;
use App\Http\Validations\PumpOperator\FarmerPumpOperatorReniewsRejectValidations;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use DB;

class FarmerPumpOperatorApplicationReniewsController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * get all Farmer Pump Operator Rejects
     */
    public function index(Request $request)
    {
        $query = FarmerPumpOperatorApplicationReniews::with('pump_opt_application');

        if ($request->pump_opt_apps_id) {
            $query = $query->where('pump_opt_apps_id', $request->pump_opt_apps_id);
        }

        if ($request->to_date) {
            $query = $query->whereDate('application_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }


        if ($request->form_date) {
            $query = $query->whereDate('application_date', '>=', date('Y-m-d', strtotime($request->form_date)));
        }

        if ($request->payment_status) {
            $query = $query->where('payment_status', $request->payment_status);
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Notification to Renew list',
            'data' => $list
        ]);
    }

    /**
     * Farmer Pump Operator Rejects store
     */
    public function store(Request $request)
    {
        try {

            $FarmerPumpOperatorApplicationReniews                     = new FarmerPumpOperatorApplicationReniews();
            $FarmerPumpOperatorApplicationReniews->pump_opt_apps_id   = (int)$request->pump_opt_apps_id;
            $FarmerPumpOperatorApplicationReniews->application_date   = date("Y-m-d");
            $FarmerPumpOperatorApplicationReniews->payment_status     = 1;
			$FarmerPumpOperatorApplicationReniews->save();

            save_log([
                'data_id'    => $FarmerPumpOperatorApplicationReniews->id,
                'table_name' => 'far_pump_opt_rejects'
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $FarmerPumpOperatorApplicationReniews
        ]);
    }

     /**
     * Pump Operator Application status update
     */

    public function updateAsProcessing($id)
    {
        $FarmerPumpOperatorApplicationReniews = FarmerPumpOperatorApplicationReniews::find($id);

        if (!$FarmerPumpOperatorApplicationReniews) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }
        $current_status= $FarmerPumpOperatorApplicationReniews->status;

        if( $current_status == 0 ){
	        $FarmerPumpOperatorApplicationReniews->status = 1;
	        $FarmerPumpOperatorApplicationReniews->update();

        }elseif($current_status == 2){
        	return response([
	            'success' => true,
	            'message' => 'Application status already Approved',
	            'data'    => $FarmerPumpOperatorApplicationReniews
	        ]);

        }else{
        	return response([
	            'success' => true,
	            'message' => 'Application status already processing or rejected',
	            'data'    => $FarmerPumpOperatorApplicationReniews
	        ]);
        }

        save_log([
            'data_id' => $FarmerPumpOperatorApplicationReniews->id,
            'table_name' => 'far_pump_opt_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Application statas update as processing',
            'data'    => $FarmerPumpOperatorApplicationReniews
        ]);
    }



    public function updateAsApproved($id)
    {
        $FarmerPumpOperatorApplicationReniews = FarmerPumpOperatorApplicationReniews::find($id);

        if (!$FarmerPumpOperatorApplicationReniews) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $current_status= $FarmerPumpOperatorApplicationReniews->status;

        if( $current_status == 1 || $current_status == 0 ){
	        $FarmerPumpOperatorApplicationReniews->status = 2;

	        $FarmerPumpOperatorApplicationReniews->update();

        }elseif($current_status == 2){
        	return response([
	            'success' => true,
	            'message' => 'Application status already Approved',
	            'data'    => $FarmerPumpOperatorApplicationReniews
	        ]);
        }else{
        	return response([
	            'success' => true,
	            'message' => 'Application status already rejected',
	            'data'    => $FarmerPumpOperatorApplicationReniews
	        ]);
        }

        save_log([
            'data_id' => $FarmerPumpOperatorApplicationReniews->id,
            'table_name' => 'far_pump_opt_apps',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Application statas update as approved',
            'data'    => $FarmerPumpOperatorApplicationReniews
        ]);
    }


    public function updateAsReject(Request $request,$id)
    {
        $FarmerPumpOperatorApplicationReniews = FarmerPumpOperatorApplicationReniews::find($id);

        if (!$FarmerPumpOperatorApplicationReniews) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $current_status= $FarmerPumpOperatorApplicationReniews->status;

        if( $current_status == 3 ){

        	return response([
	            'success' => true,
	            'message' => 'Application statas already rejected',
	            'data'    => $FarmerPumpOperatorApplicationReniews
            ]);

        } else {

            $FarmerPumpOperatorApplicationReniews->status = 3;

            try {
                if($FarmerPumpOperatorApplicationReniews->update()){

                    $validationResult = FarmerPumpOperatorReniewsRejectValidations:: validate($request);
                    if (!$validationResult['success']) {
                        return response($validationResult);
                    }

                    $file_path      = 'pump-operator-reniews-reject';
                    $attachment     =  $request->file('attachment');
                    $scheme_application_id = $FarmerPumpOperatorApplicationReniews->pump_opt_apps_id;


                    $FarmerPumpOperatorReniewsReject                       = new FarmerPumpOperatorReniewsReject();
                    $FarmerPumpOperatorReniewsReject->scheme_application_id =  (int)$scheme_application_id;
                    $FarmerPumpOperatorReniewsReject->renew_id              =  (int)$id;
                    $FarmerPumpOperatorReniewsReject->reject_note           =  $request->reject_note;
                    $FarmerPumpOperatorReniewsReject->reject_note_bn        =  $request->reject_note_bn;


                    if($attachment !=null && $attachment !=""){
                        $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
                    }

                    $FarmerPumpOperatorReniewsReject->attachment        =  $attachment_name ? $attachment_name : null;

                    if($FarmerPumpOperatorReniewsReject->save()){
                        GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
                    }

                    save_log([
                        'data_id' => $FarmerPumpOperatorApplicationReniews->id,
                        'table_name' => 'far_pump_opt_apps',
                        'execution_type' => 2
                    ]);

                }

            } catch (\Exception $ex) {
                return response([
                    'success' => false,
                    'message' => 'Failed to save data.',
                    'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
                ]);
            }

        return response([
            'success' => true,
            'message' => 'Application statas update as reject',
            'data'    => $FarmerPumpOperatorApplicationReniews
        ]);
    }

}
}
