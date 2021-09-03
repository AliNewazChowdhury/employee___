<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\SchemeRejectValidation;
use App\Library\SmsLibrary;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\SchemeReject;
use Illuminate\Http\Request;
use DB;

class SchemeRejectController extends Controller
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
     * get all Scheme reject
     */
    public function index(Request $request)
    {
        $query = DB::table('far_scheme_rejects')
                    ->join('far_scheme_application','far_scheme_rejects.scheme_application_id', '=','far_scheme_application.id')                
                    ->select('far_scheme_rejects.*',
                        'far_scheme_application.name as farmer_name','far_scheme_application.name_bn as farmer_name_bn'
             );

        if ($request->scheme_application_id) {
            $query = $query->where('scheme_application_id', $request->scheme_application_id);           
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        

        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Scheme reject list',
            'data' => $list
        ]);
    }

    /**
     * Scheme reject  store
     */
    public function store(Request $request)
    {  
        $validationResult = SchemeRejectValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();

        try {

            $farmer_sch_app         = FarmerSchemeApplication::find($request->scheme_application_id);
            $farmer_sch_app->status = $request->status;
            $farmer_sch_app->update();

            if ($request->supervisor_phone != null) {
                $smsData['mobile']  = $request->supervisor_phone;
                $smsData['message'] = "Scheme Application (ID :". $farmer_sch_app->application_id . ') is rejected';
                $sms = new SmsLibrary();
                $sms->sms_helper($smsData); 
            }                       

            save_log([
                'data_id'       => $farmer_sch_app->id,
                'table_name'    => 'far_scheme_application',
                'execution_type'=> 1
            ]);

            $scheme_review                         = new SchemeReject();
            $scheme_review->scheme_application_id  = (int)$request->scheme_application_id;
            $scheme_review->reject_note	           = $request->reject_note;
            $scheme_review->reject_note_bn	       = $request->reject_note_bn;
            $scheme_review->created_by             = (int)user_id();
            $scheme_review->updated_by             = (int)user_id();
            $scheme_review->save();

            save_log([
                'data_id'   => $scheme_review->id,
                'table_name'=> 'far_scheme_rejects'
            ]);

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
            'data'    => $farmer_sch_app
        ]);
    }

    /**
     * Scheme reject update
     */
    public function update(Request $request, $id)
    {
        $validationResult = SchemeRejectValidation:: validate($request, $id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $scheme_review = SchemeReject::find($id);

        if (!$scheme_review) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $scheme_review->scheme_application_id   = (int)$request->scheme_application_id;
            $scheme_review->reject_note	            = $request->reject_note;
            $scheme_review->reject_note_bn	        = $request->reject_note_bn;
            $scheme_review->updated_by              = (int)user_id();
            $scheme_review->save();

            save_log([
                'data_id'       => $scheme_review->id,
                'table_name'    => 'far_scheme_rejects',
                'execution_type'=> 1
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $scheme_review
        ]);
    }

    /**
     * Scheme reject destroy
     */
    public function destroy($id)
    {
        $scheme_review = SchemeReject::find($id);

        if (!$scheme_review) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $scheme_review->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_scheme_rejects',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
