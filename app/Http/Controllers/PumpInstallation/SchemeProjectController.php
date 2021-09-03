<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\SchemeProjectValidation;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\SchemeProject;
use Illuminate\Http\Request;
use DB;

class SchemeProjectController extends Controller
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
     * get all Scheme project
     */
    public function index(Request $request)
    {
        $query = DB::table('far_scheme_projects')
                ->join('far_scheme_application','far_scheme_projects.scheme_application_id', '=','far_scheme_application.id')
                ->join('master_projects','far_scheme_projects.project_id', '=','master_projects.id')
                ->select('far_scheme_projects.*',
                        'far_scheme_application.name as farmer_name','far_scheme_application.name_bn as farmer_name_bn',
                        'master_projects.project_name as project_name','master_projects.project_name_bn as project_name_bn'
             );

        if ($request->scheme_application_id) {
            $query = $query->where('scheme_application_id', $request->scheme_application_id);           
        }

        if ($request->project_id) {
            $query = $query->where('project_id', $request->project_id);           
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }        

        $list = $query->paginate($request->per_page);

        return response([
            'success' => true,
            'message' => 'Scheme project list',
            'data' => $list
        ]);
    }

    /**
     * Scheme project  store
     */
    public function store(Request $request)
    {  
        $validationResult = SchemeProjectValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();

        try {
            
            $existSchemeProject = SchemeProject::where('scheme_application_id', $request->scheme_application_id)->first();

            if ($existSchemeProject != null) {
                $scheme_project = SchemeProject::find($existSchemeProject->id);
                $scheme_project->project_id  = (int)$request->project_id;
                $scheme_project->update();
            } else {
                $scheme_project                         = new SchemeProject();
                $scheme_project->scheme_application_id  = (int)$request->scheme_application_id;
                $scheme_project->project_id	            = (int)$request->project_id;
                $scheme_project->created_by             = (int)user_id();
                $scheme_project->updated_by             = (int)user_id();
                $scheme_project->save();
            }            

            save_log([
                'data_id'   => $scheme_project->id,
                'table_name'=> 'far_scheme_projects'
            ]);
            

            $farmer_sch_app         = FarmerSchemeApplication::find($request->scheme_application_id);
            $farmer_sch_app->status = $request->status;
            $farmer_sch_app->update();

            save_log([
                'data_id'       => $farmer_sch_app->id,
                'table_name'    => 'far_scheme_application',
                'execution_type'=> 1
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
     * Scheme project update
     */
    public function update(Request $request, $id)
    {
        $validationResult = SchemeProjectValidation:: validate($request, $id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $scheme_project = SchemeProject::find($id);

        if (!$scheme_project) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $scheme_project->scheme_application_id  = (int)$request->scheme_application_id;
            $scheme_project->project_id	            = (int)$request->project_id;
            $scheme_project->updated_by             = (int)user_id();
            $scheme_project->save();

            save_log([
                'data_id'       => $scheme_project->id,
                'table_name'    => 'far_scheme_projects',
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
            'data'    => $scheme_project
        ]);
    }

    /**
     * Scheme project destroy
     */
    public function destroy($id)
    {
        $scheme_project = SchemeProject::find($id);

        if (!$scheme_project) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $scheme_project->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_scheme_projects',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
