<?php

namespace App\Http\Controllers\PumpInfoManagement;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\PumpInfoManagement\PumpSchedulerValidation;
use App\Models\PumpInfoManagement\PumpScheduler;

class PumpSchedulerController extends Controller
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
     * get all  pump Scheduler
     */
    public function index(Request $request)
    {
        $query = PumpScheduler::select('*');

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->pump_id) {
            $query = $query->where('pump_id', $request->pump_id);
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        $list = $query->paginate($request->per_page);

        return response([
            'success' => true,
            'message' => 'Pump Scheduler list',
            'data' => $list
        ]);
    }

    /**
     * get all  pump Scheduler
     */
    public function listAll(Request $request)
    {
        $query = PumpScheduler::select('*')->where('status',0)->get();

        return response([
            'success' => true,
            'message' => 'Pump Scheduler list',
            'data' => $query
        ]);
    }

    public function byPump_id()
    {
        $query = PumpScheduler::orderBy('pump_id')->get();

        return response([
            'success' => true,
            'message' => 'Pump Scheduler list',
            'data' => $query
        ]);
    }

    /**
     * pump Scheduler store 
     */
    public function store(Request $request)
    {  
        $validationResult = PumpSchedulerValidation:: validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {
            $pumpScheduler                       = new PumpScheduler();
            $pumpScheduler->org_id               = (int)$request->org_id;
            $pumpScheduler->pump_id              = (int)$request->pump_id;
            $pumpScheduler->ontime               = $request->ontime;
            $pumpScheduler->offtime              = $request->offtime;    
            $pumpScheduler->created_by           = (int)user_id();
            $pumpScheduler->updated_by           = (int)user_id();
            $pumpScheduler->save();

            save_log([
                'data_id' => $pumpScheduler->id,
                'table_name' => 'pump_schedulers',
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
            'data'    => $pumpScheduler
        ]);
    }

    /**
     * pump Scheduler  Update
     */
    public function update(Request $request, $id)
    {
        $validationResult = PumpSchedulerValidation:: validate($request ,$id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $pumpScheduler = PumpScheduler::find($id);

        if (!$pumpScheduler) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $pumpScheduler->org_id               = (int)$request->org_id;
            $pumpScheduler->pump_id              = (int)$request->pump_id;
            $pumpScheduler->ontime               = $request->ontime;
            $pumpScheduler->offtime              = $request->offtime;   
            $pumpScheduler->updated_by           = (int)user_id();
           
            $pumpScheduler->update();

            save_log([
                'data_id' => $pumpScheduler->id,
                'table_name' => 'pump_schedulers',
                'execution_type' => 1
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
            'data'    => $pumpScheduler
        ]);
    }

    /**
    * Pump Scheduler Toggle Status
     */
    public function toggleStatus($id)
    {
        $pumpScheduler = PumpScheduler::find($id);

        if (!$pumpScheduler) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $pumpScheduler->status = $pumpScheduler->status ? 0 : 1;
        $pumpScheduler->update();

        save_log([
            'data_id' => $pumpScheduler->id,
            'table_name' => 'pump_schedulers',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $pumpScheduler
        ]);
    }

    /**
     * Pump Scheduler Delete
     */
    public function destroy($id)
    {
        $pumpScheduler = PumpScheduler::find($id);

        if (!$pumpScheduler) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $pumpScheduler->delete();

        save_log([
            'data_id' => $pumpScheduler->id,
            'table_name' => 'pump_schedulers',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
