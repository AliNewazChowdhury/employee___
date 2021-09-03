<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\SchemeSurveyValidation;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\SchemeSurvery;
use App\Models\PumpInstallation\SchemeNote;
use Illuminate\Http\Request;
use DB;

class SchemeSurveyController extends Controller
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
     * get all scheme survey
     */
    public function index(Request $request)
    {  
        $query = DB::table('far_scheme_surveys')
                        ->leftjoin('far_scheme_application','far_scheme_surveys.scheme_application_id', '=', 'far_scheme_application.id')
                        ->select('far_scheme_application.application_id','far_scheme_application.id','far_scheme_surveys.survey_date',
                            'far_scheme_surveys.suggestion','far_scheme_surveys.suggestion_bn'
                        );

        if ($request->scheme_application_id) {
            $query = $query->where('far_scheme_surveys.scheme_application_id', $request->scheme_application_id);           
        }

        if ($request->survey_date) {
            $query = $query->whereDate('far_scheme_surveys.survey_date', date('Y-m-d', strtotime($request->survey_date)));
        }

        $list = $query->first();

        return response([
            'success' => true,
            'message' => 'Scheme survey list',
            'data' => $list
        ]);
    }

    /**
     * scheme survey  store
     */
    public function store(Request $request)
    {   
        $validationResult = SchemeSurveyValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();

        try {

            $farmer_sch_app         = FarmerSchemeApplication::find($request->scheme_application_id);
            $farmer_sch_app->status = $request->status;
            $farmer_sch_app->update();

            save_log([
                'data_id'       => $farmer_sch_app->id,
                'table_name'    => 'far_scheme_application',
                'execution_type'=> 1
            ]);

            $scheme_survery                         = new SchemeSurvery();
            $scheme_survery->scheme_application_id  = (int)$request->scheme_application_id;
            $scheme_survery->survey_date            = date('Y-m-d', strtotime($request->survey_date));
            $scheme_survery->suggestion             = $request->suggestion;
            $scheme_survery->suggestion_bn          = $request->suggestion_bn;
            $scheme_survery->created_by             = (int)user_id();
            $scheme_survery->updated_by             = (int)user_id();
            $scheme_survery->save();

            save_log([
                'data_id'   => $scheme_survery->id,
                'table_name'=> 'far_scheme_surveys'
            ]);

            foreach($request->notes as $note) { 
                $ssn                          = new SchemeNote();
                $ssn->scheme_application_id   = (int)$note['scheme_application_id'];
                $ssn->note                    = $note['note'];
                $ssn->note_bn                 = $note['note_bn'];
                $ssn->created_by              = (int)user_id();
                $ssn->updated_by              = (int)user_id();
                $ssn->save();
            }        

            save_log([
                'data_id'   => $ssn->id,
                'table_name'=> 'far_scheme_notes'
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
     * scheme survey update
     */
    public function update(Request $request, $id)
    {
        $validationResult = SchemeSurveyValidation:: validate($request, $id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $scheme_survery = SchemeSurvery::find($id);

        if (!$scheme_survery) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {
            $scheme_survery->scheme_application_id  = (int)$request->scheme_application_id;
            $scheme_survery->survey_date            = date('Y-m-d', strtotime($request->survey_date));
            $scheme_survery->suggestion             = $request->suggestion;
            $scheme_survery->suggestion_bn          = $request->suggestion_bn;
            $scheme_survery->updated_by             = (int)user_id();
            $scheme_survery->save();

            save_log([
                'data_id'       => $scheme_survery->id,
                'table_name'    => 'far_scheme_surveys',
                'execution_type'=> 1
            ]);

            SchemeNote::where('scheme_application_id',$request->scheme_application_id)->delete();

            foreach($request->notes as $note) { 
                $ssn                          = new SchemeNote();
                $ssn->scheme_application_id   = (int)$note['scheme_application_id'];
                $ssn->note                    = $note['note'];
                $ssn->note_bn                 = $note['note_bn'];
                $ssn->created_by              = (int)user_id();
                $ssn->updated_by              = (int)user_id();
                $ssn->save();
            }     

            save_log([
                'data_id'   => $ssn->id,
                'table_name'=> 'far_scheme_notes'
            ]);

            DB::commit();

        } catch (\Exception $ex) {

             DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $scheme_survery
        ]);
    }

    /**
     * scheme survey destroy
     */
    public function destroy($id)
    {               
        $scheme_survery = SchemeSurvery::find($id);

        SchemeNote::where('scheme_application_id',$scheme_survery->scheme_application_id)->delete();

        if (!$scheme_survery) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $scheme_survery->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_scheme_surveys',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
