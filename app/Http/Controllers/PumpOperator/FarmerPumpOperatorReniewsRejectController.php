<?php

namespace App\Http\Controllers\PumpOperator;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpOperator\FarmerPumpOperatorReniewsRejectValidations;
use App\Models\PumpOperator\FarmerPumpOperatorApplicationReniews;
use App\Models\PumpOperator\FarmerPumpOperatorReniewsReject;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use DB;

class FarmerPumpOperatorReniewsRejectController extends Controller
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
        $query = FarmerPumpOperatorReniewsReject::with('pump_operator_reniews');

        if ($request->reject_note) {
            $query = $query->where('reject_note', 'like', "{$request->reject_note}%")
                            ->orWhere('reject_note_bn', 'like', "{$request->reject_note}%");
        }

        if ($request->scheme_application_id) {
            $query = $query->where('scheme_application_id', $request->scheme_application_id);
        }

        if ($request->renew_id) {
            $query = $query->where('renew_id', $request->renew_id);
        }

        // $perPage = env('PER_PAGE');
        $list = $query->paginate($request->per_page); 

        return response([
            'success' => true,
            'message' => 'Pump operator reniews reject list',
            'data' => $list
        ]);
    }


    /**
     * Farmer Pump Operator Rejects store
     */
    public function store(Request $request)
    {
        $validationResult = FarmerPumpOperatorReniewsRejectValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path      = 'pump-operator-reniews-reject';
        $attachment     =  $request->file('attachment');

        $renew_id = $request->renew_id;
        $ReniewsModel = FarmerPumpOperatorApplicationReniews::find($renew_id); 

        $scheme_application_id = $ReniewsModel->pump_opt_apps_id;


        DB::beginTransaction();
        try {
            $FarmerPumpOperatorReniewsReject                       = new FarmerPumpOperatorReniewsReject();
            $FarmerPumpOperatorReniewsReject->scheme_application_id =  (int)$scheme_application_id;
            $FarmerPumpOperatorReniewsReject->renew_id              =  (int)$renew_id;
            $FarmerPumpOperatorReniewsReject->reject_note           =  $request->reject_note;
            $FarmerPumpOperatorReniewsReject->reject_note_bn        =  $request->reject_note_bn;


            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
            }

            $FarmerPumpOperatorReniewsReject->attachment        =  $attachment_name ? $attachment_name : null;

            if($FarmerPumpOperatorReniewsReject->save()){

                 GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
            }


            DB::commit();

            save_log([
                'data_id'    => $FarmerPumpOperatorReniewsReject->id,
                'table_name' => 'far_pump_opt_reniews_reject'
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
            'data'    => $FarmerPumpOperatorReniewsReject
        ]);
    }

    /**
     * Farmer Pump Operator Rejects update
     */
    public function update(Request $request, $id)
    {
        $validationResult = FarmerPumpOperatorReniewsRejectValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path      = 'pump-operator-reniews-reject';
        $attachment     =  $request->file('attachment');

        $FarmerPumpOperatorReniewsReject  = FarmerPumpOperatorReniewsReject::find($id);
        $old_file                   = $FarmerPumpOperatorReniewsReject->attachment;

        if (!$FarmerPumpOperatorReniewsReject) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {
            $FarmerPumpOperatorReniewsReject->reject_note     =  $request->reject_note;
            $FarmerPumpOperatorReniewsReject->reject_note_bn  =  $request->reject_note_bn;

            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request,$file_path,'attachment');
                $FarmerPumpOperatorReniewsReject->attachment    =  $attachment_name;
                if($FarmerPumpOperatorReniewsReject->save()){
                     GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name, $old_file );
                }
            }else{
               $FarmerPumpOperatorReniewsReject->save(); 
            }


            DB::commit();
            save_log([
                'data_id'       => $FarmerPumpOperatorReniewsReject->id,
                'table_name'    => 'far_pump_opt_reniews_reject',
                'execution_type'=> 1
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $FarmerPumpOperatorReniewsReject
        ]);
    }

    

    public function destroy($id)
    {
        $FarmerPumpOperatorReniewsReject = FarmerPumpOperatorReniewsReject::find($id);

        if (!$FarmerPumpOperatorReniewsReject) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $FarmerPumpOperatorReniewsReject->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_pump_opt_reniews_reject',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

   
}
