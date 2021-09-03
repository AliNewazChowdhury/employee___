<?php
namespace App\Http\Controllers\PumpInfoManagement;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\PumpInfoManagement\PumpConstructionDetails;
use App\Http\Validations\PumpInfoManagement\PumpConstructionDetailsValidations;

class PumpConstructionDetailsController extends Controller
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
     * get all Pump construction details
     */
    public function index(Request $request)
    {
        $query = DB::table('pump_construction_details')
                        ->select('pump_construction_details.*');

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->division_id) {
            $query = $query->where('division_id', $request->division_id);
        } 

        if ($request->district_id) {
            $query = $query->where('district_id', $request->district_id);
        }

        if ($request->upazilla_id) {
            $query = $query->where('upazilla_id', $request->upazilla_id);
        }

        if ($request->union_id) {
            $query = $query->where('union_id', $request->union_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_id', $request->pump_id);
        }

        if ($request->service_id) {
            $query = $query->where('service_id', $request->service_id);
        }

        if ($request->mouza_no) {
            $query = $query->where('mouza_no', 'like', "{$request->mouza_no}%")
                            ->orWhere('mouza_no_bn', 'like', "{$request->mouza_no}%");
        }

        if ($request->jl_no) {
            $query = $query->where('jl_no', 'like', "{$request->jl_no}%")
                            ->orWhere('jl_no_bn', 'like', "{$request->jl_no}%");
        }

        if ($request->plot_no) {
            $query = $query->where('plot_no', 'like', "{$request->plot_no}%")
                            ->orWhere('plot_no_bn', 'like', "{$request->plot_no}%");
        }

        if ($request->project_id) {
            $query = $query->where('project_id', $request->project_id);
        }

        if ($request->well_no) {
            $query = $query->where('well_no', 'like', "{$request->well_no}%")
                            ->orWhere('well_no_bn', 'like', "{$request->well_no}%");
        }

        if ($request->engineer_name) {
            $query = $query->where('engineer_name', 'like', "{$request->engineer_name}%")
                            ->orWhere('engineer_name_bn', 'like', "{$request->engineer_name}%");
        }

        if ($request->drilling_contractor_name) {
            $query = $query->where('drilling_contractor_name', 'like', "{$request->drilling_contractor_name}%")
                            ->orWhere('drilling_contractor_name_bn', 'like', "{$request->drilling_contractor_name}%");
        }
       
        if ($request->drilling_start_date)
        {
            $query = $query->whereDate('drilling_start_date', '>=', date('Y-m-d', strtotime($request->drilling_start_date)));
        }
        
        if ($request->drilling_complete_date)
        {
            $query = $query->whereDate('drilling_complete_date', '<=', date('Y-m-d', strtotime($request->drilling_complete_date)));
        }        

      
        $list = $query->paginate($request->per_page??10);            

        if(count($list) > 0){
            return response([
                'success' => true,
                'message' => "Pump construction details list",
                'data' => $list
            ]);

        }else{
            return response([
                'success' => false,
                'message' => "Data not found!!"
            ]);
        }
       
    }

    /**
     *  construction details Report
     */
    public function report(Request $request)
    {
        $query = DB::table('pump_construction_details')
                        ->select('pump_construction_details.*');

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->division_id) {
            $query = $query->where('division_id', $request->division_id);
        } 

        if ($request->district_id) {
            $query = $query->where('district_id', $request->district_id);
        }

        if ($request->upazilla_id) {
            $query = $query->where('upazilla_id', $request->upazilla_id);
        }

        if ($request->union_id) {
            $query = $query->where('union_id', $request->union_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_id', $request->pump_id);
        }

        if ($request->service_id) {
            $query = $query->where('service_id', $request->service_id);
        }

        if ($request->mouza_no) {
            $query = $query->where('mouza_no', 'like', "{$request->mouza_no}%")
                            ->orWhere('mouza_no_bn', 'like', "{$request->mouza_no}%");
        }

        if ($request->jl_no) {
            $query = $query->where('jl_no', 'like', "{$request->jl_no}%")
                            ->orWhere('jl_no_bn', 'like', "{$request->jl_no}%");
        }

        if ($request->plot_no) {
            $query = $query->where('plot_no', 'like', "{$request->plot_no}%")
                            ->orWhere('plot_no_bn', 'like', "{$request->plot_no}%");
        }

        if ($request->project_id) {
            $query = $query->where('project_id', $request->project_id);
        }

        if ($request->well_no) {
            $query = $query->where('well_no', 'like', "{$request->well_no}%")
                            ->orWhere('well_no_bn', 'like', "{$request->well_no}%");
        }

        if ($request->engineer_name) {
            $query = $query->where('engineer_name', 'like', "{$request->engineer_name}%")
                            ->orWhere('engineer_name_bn', 'like', "{$request->engineer_name}%");
        }

        if ($request->drilling_contractor_name) {
            $query = $query->where('drilling_contractor_name', 'like', "{$request->drilling_contractor_name}%")
                            ->orWhere('drilling_contractor_name_bn', 'like', "{$request->drilling_contractor_name}%");
        }
       
        if ($request->drilling_start_date)
        {
            $query = $query->whereDate('drilling_start_date', '>=', date('Y-m-d', strtotime($request->drilling_start_date)));
        }
        
        if ($request->drilling_complete_date)
        {
            $query = $query->whereDate('drilling_complete_date', '<=', date('Y-m-d', strtotime($request->drilling_complete_date)));
        }        

      
        $list = $query->get();            

        if(count($list) > 0){
            return response([
                'success' => true,
                'message' => "Pump construction details list",
                'data' => $list
            ]);

        }else{
            return response([
                'success' => false,
                'message' => "Data not found!!"
            ]);
        }
       
    }


    public function show($id)
    {
        $query = PumpConstructionDetails::find($id);

        if(!$query){
            return response([
                'success' => false,
                'message' => "Data Not found"
            ]);
        }

        return response([
            'success' => true,
            'message' => "Pump construction details list",
            'data' => $query
        ]);
       
    }

    public function store(Request $request)
    {   
        $validationResult = PumpConstructionDetailsValidations::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {
            $PumpConstructionDetails = new PumpConstructionDetails();

            $data = $request->all();
            $data['created_by']      = user_id()??null;  
            $PumpConstructionDetails->create($data);  

            save_log([
                'data_id'   => $PumpConstructionDetails->id,
                'table_name'=> 'pump_construction_details'
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
            'data'    => $PumpConstructionDetails
        ]);
    }

    /**
     * Pump construction details update
     */
    public function update(Request $request, $id)
    {
        $validationResult = PumpConstructionDetailsValidations:: validate($request,$id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }
        $PumpConstructionDetails = PumpConstructionDetails::find($id);

        if (!$PumpConstructionDetails) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }
        
        $data = $request->all(); 
        try {
            
            $data['updated_by']      = user_id()??null;
            
            $PumpConstructionDetails->update($data);

            save_log([
                'data_id'       => $PumpConstructionDetails->id,
                'table_name'    => 'pump_construction_details',
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
            'data'    => $PumpConstructionDetails
        ]);
    }


    /**
     * Pump construction details status update
     */
    public function toggleStatus($id)
    {
        $PumpConstructionDetails = PumpConstructionDetails::find($id);

        if (!$PumpConstructionDetails) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $PumpConstructionDetails->status = $PumpConstructionDetails->status ? 0 : 1;
        $PumpConstructionDetails->update();

        save_log([
            'data_id'       => $PumpConstructionDetails->id,
            'table_name'    => 'pump_construction_details',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $PumpConstructionDetails
        ]);
    }
 
    /**
     * Pump construction details destroy
     */
    public function destroy($id)
    {
        $PumpConstructionDetails = PumpConstructionDetails::find($id);

        if (!$PumpConstructionDetails) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $PumpConstructionDetails->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'pump_construction_details',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}