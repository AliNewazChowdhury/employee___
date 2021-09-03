<?php

namespace App\Http\Controllers\PumpInstallation;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PumpInstallation\DeepTubewellYearlyFinalReport;
use App\Http\Validations\PumpInstallation\DeepTubewellYearlyFinalReportValidations;

class DeepTubewellYearlyFinalReportController extends Controller
{	
	/**
     * get all Deep tubewell yearly final report
     */
    public function index(Request $request)
    {  
        $query = DB::table('deep_tubewell_yearly_final_report')->select('deep_tubewell_yearly_final_report.*');
        
        if($request->org_id){
            $query = $query->where('org_id', $request->org_id);
        }
        
        if($request->pump_id){
            $query = $query->where('pump_id', $request->pump_id);
        }
        
        if($request->division_id){
            $query = $query->where('division_id', $request->division_id);
        }
        
        if($request->district_id){
            $query = $query->where('district_id', $request->district_id);
        }
        
        if($request->upazilla_id){
            $query = $query->where('upazilla_id', $request->upazilla_id);
        }
        
        if($request->union_id){
            $query = $query->where('union_id', $request->union_id);
        }

        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Deep tubewell yearly final report list',
            'data' => $list
        ]);
    }

	/**
     * get all Deep tubewell yearly final report without pagination
     */
    public function indexAll(Request $request)
    {  
        $query = DB::table('deep_tubewell_yearly_final_report')->select('deep_tubewell_yearly_final_report.*');
        
        if($request->org_id){
            $query = $query->where('org_id', $request->org_id);
        }
        
        if($request->pump_id){
            $query = $query->where('pump_id', $request->pump_id);
        }
        
        if($request->division_id){
            $query = $query->where('division_id', $request->division_id);
        }
        
        if($request->district_id){
            $query = $query->where('district_id', $request->district_id);
        }
        
        if($request->upazilla_id){
            $query = $query->where('upazilla_id', $request->upazilla_id);
        }
        
        if($request->union_id){
            $query = $query->where('union_id', $request->union_id);
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Deep tubewell yearly final report list',
            'data' => $list
        ]);
    }

    public function show($id)
    {
        $query = DeepTubewellYearlyFinalReport::find($id);

        if(!$query){
            return response([
                'success' => false,
                'message' => "Data Not found"
            ]);
        }

        return response([
            'success' => true,
            'message' => "Master contents list",
            'data' => $query
        ]);
       
    }

    /**
     * Store Deep tubewell yearly final report
     */
    public function store(Request $request)
    {   
        $validationResult = DeepTubewellYearlyFinalReportValidations::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {
        	$dTubeYearFiRe = new DeepTubewellYearlyFinalReport();

            $data = $request->all();
			
			$dTubeYearFiRe->create($data);  

            save_log([
                'data_id'   => $dTubeYearFiRe->id,
                'table_name'=> 'deep_tubewell_yearly_final_report'
            ]);

        } catch (\Exception $ex) {

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $dTubeYearFiRe
        ]);
    }

        /**
     * Master contents update
     */
    public function update(Request $request, $id)
    {
        $validationResult = DeepTubewellYearlyFinalReportValidations:: validate($request,$id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $deepTubwellEntry = DeepTubewellYearlyFinalReport::find($id);

        if (!$deepTubwellEntry) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $data = $request->all(); 
        try {
            
            $data['updated_by']      = user_id()??null;
            
            $deepTubwellEntry->update($data);

            save_log([
                'data_id'       => $deepTubwellEntry->id,
                'table_name'    => 'deep_tubewell_yearly_final_report',
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
            'data'    => $deepTubwellEntry
        ]);
    }

        /**
     * Master contents status update
     */
    public function toggleStatus($id)
    {
        $deepTubwellEntry = DeepTubewellYearlyFinalReport::find($id);

        if (!$deepTubwellEntry) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $deepTubwellEntry->status = $deepTubwellEntry->status ? 0 : 1;
        $deepTubwellEntry->update();

        save_log([
            'data_id'       => $deepTubwellEntry->id,
            'table_name'    => 'deep_tubewell_yearly_final_report',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $deepTubwellEntry
        ]);
    }
}
