<?php
namespace App\Http\Controllers\PumpOperator;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpOperator\FarmerPumpOperatorRejectsValidations;
use App\Models\PumpOperator\FarmerPumpOperatorApplication;
use App\Models\PumpOperator\FarmerPumpOperatorRejects;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use App\Library\SmsLibrary;
use DB;

class FarmerPumpOperatorRejectsController extends Controller
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
        $query = FarmerPumpOperatorRejects::with('pump_opt_application');

        if ($request->reject_note) {
            $query = $query->where('reject_note', 'like', "{$request->reject_note}%")
                            ->orWhere('reject_note_bn', 'like', "{$request->reject_note}%");
        }

        if ($request->pump_opt_apps_id) {
            $query = $query->where('pump_opt_apps_id', $request->pump_opt_apps_id);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }

    /**
     * Farmer Pump Operator Rejects store
     */
    public function store(Request $request)
    {   
        $validationResult = FarmerPumpOperatorRejectsValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path  = 'pump-operator-rejects';
        $attachment =  $request->file('attachment');

        DB::beginTransaction();

        try {

            $fpoa         = FarmerPumpOperatorApplication::find($request->pump_opt_apps_id);
            $fpoa->status = 4;
            $fpoa->update();

            if ($request->supervisor_phone != null) {
                $smsData['mobile']  = $request->supervisor_phone;
                $smsData['message'] = "Pump Operator Application (ID :". $fpoa->application_id . ') is rejected';
                $sms = new SmsLibrary();
                $sms->sms_helper($smsData); 
            }    

            save_log([
                'data_id'       => $fpoa->id,
                'table_name'    => 'far_scheme_application',
                'execution_type'=> 1
            ]);

            $FarmerPumpOperatorRejects                     = new FarmerPumpOperatorRejects();
            $FarmerPumpOperatorRejects->pump_opt_apps_id   = (int)$request->pump_opt_apps_id;
            $FarmerPumpOperatorRejects->reject_note        = $request->reject_note;
            $FarmerPumpOperatorRejects->reject_note_bn     = $request->reject_note_bn;

            if($attachment != null && $attachment != ""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
            }

            $FarmerPumpOperatorRejects->attachment  =  isset($attachment_name) ? $attachment_name : null;

            if($FarmerPumpOperatorRejects->save() && isset($attachment_name)){
                GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
            }

            DB::commit();

            save_log([
                'data_id'    => $FarmerPumpOperatorRejects->id,
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
            'message' => 'Data update successfully',
            'data'    => $fpoa
        ]);
    }

    /**
     * Farmer Pump Operator Rejects update
     */
    public function update(Request $request, $id)
    {
        $validationResult = FarmerPumpOperatorRejectsValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path      = 'pump-operator-rejects';
        $attachment     =  $request->file('attachment');

        $FarmerPumpOperatorRejects  = FarmerPumpOperatorRejects::find($id);
        $old_file                   = $FarmerPumpOperatorRejects->attachment;

        if (!$FarmerPumpOperatorRejects) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {

            $FarmerPumpOperatorRejects->pump_opt_apps_id   =  (int)$request->pump_opt_apps_id;
            $FarmerPumpOperatorRejects->reject_note        =  $request->reject_note;
            $FarmerPumpOperatorRejects->reject_note_bn     =  $request->reject_note_bn;

            if($attachment != null && $attachment != ""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request,$file_path,'attachment');
                $FarmerPumpOperatorRejects->attachment    =  $attachment_name;
	            if($FarmerPumpOperatorRejects->save()){
	                 GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name, $old_file );
	            }
            }else{
            	$FarmerPumpOperatorRejects->save();
            }


            DB::commit();
            save_log([
                'data_id'       => $FarmerPumpOperatorRejects->id,
                'table_name'    => 'far_pump_opt_rejects',
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
            'data'    => $FarmerPumpOperatorRejects
        ]);
    } 

    public function destroy($id)
    {
        $FarmerPumpOperatorRejects = FarmerPumpOperatorRejects::find($id);

        if (!$FarmerPumpOperatorRejects) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $FarmerPumpOperatorRejects->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_pump_opt_rejects',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

   
}
