<?php

namespace App\Http\Controllers\PumpMaintenance;

use App\Http\Controllers\Controller;
use App\Models\FarmerOperator\FarmerComplain;
use App\Models\PumpMaintenance\ComplainResolved;
use App\Models\PumpMaintenance\FarmerComplainProgressReport;
use Illuminate\Http\Request;
use DB;

class MaintenanceTaskController extends Controller
{
     /**
     * Farmer complain maintenace task list
     * where complain status = 5 
     */
    public function index(Request $request)
    {   
        $query = DB::table('far_complains')
                    ->Join('far_basic_infos','far_complains.farmer_id', '=','far_basic_infos.farmer_id')
                    ->leftJoin('far_complain_progress_reports','far_complains.id', '=','far_complain_progress_reports.complain_id')
                    ->leftJoin('far_complain_requisitions','far_complains.id', '=','far_complain_requisitions.complain_id')
                    ->leftJoin('far_complain_supply_equipments','far_complain_requisitions.id', '=','far_complain_supply_equipments.requisition_id')
                    ->select('far_complains.*','far_basic_infos.name_bn', 'far_basic_infos.name',
                        'far_complain_progress_reports.id as progress_report_id',
                        'far_complain_requisitions.id as complain_requisition_id',
                        'far_complain_supply_equipments.id as supply_equipment_id'
                    )
                    ->orderBy('far_complains.id','DESC')
                    ->where('far_complains.status', '>=', 5)
                    ->where('far_complains.status', '!=', 8);
        
        if ($request->org_id) {
            $query = $query->where('far_complains.org_id', $request->org_id);
        }

        if ($request->complain_id) {
            $query = $query->where('far_complains.complain_id', $request->complain_id);
        }

        if ($request->name) {
            $query = $query->where('far_basic_infos.name', 'like', "{$request->name}%")
                        ->orWhere('far_basic_infos.name_bn', 'like', "{$request->name}%");
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Complain maintenance list',
            'data' => $list
        ]);
        
    }

    /**
     * Farmer complain resolved in maintenance task
     */
    public function resolved(Request $request)
    {   
        DB::beginTransaction();

        try {

            $compResolved                   = new ComplainResolved();
            $compResolved->complain_id      = (int)$request->complain_id;
            $compResolved->resolve_note     = $request->resolve_note;
            $compResolved->resolve_note_bn  = $request->resolve_note_bn;
            $compResolved->created_by       = (int)user_id();
            $compResolved->updated_by       = (int)user_id();
            $compResolved->save();

            $farComplain = FarmerComplain::find($request->complain_id);
            $farComplain->status = 2;
            $farComplain->update();
            
            DB::commit();

            save_log([
                'data_id'       => $compResolved->id,
                'table_name'    => 'far_complain_resolves'
            ]);

        } catch (\Exception $ex) {

            DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $farComplain
        ]);        
    }

    /**
     * Farmer complain progress Report Store in maintenance task
     */
    public function progressReportStore(Request $request)
    {   
        DB::beginTransaction();

        try {

            $progressReport                 = new FarmerComplainProgressReport();
            $progressReport->complain_id    = (int)$request->complain_id;
            $progressReport->progress_type  = $request->progress_type;
            $progressReport->note           = $request->note;
            $progressReport->note_bn        = $request->note_bn;
            $progressReport->progress_date  = date('Y-m-d', strtotime($request->progress_date));
            $progressReport->save();
            
            $farComplain = FarmerComplain::find($request->complain_id);
            $farComplain->status = 7; // 7 mean complain complete
            $farComplain->update();
           
            
            DB::commit();

            save_log([
                'data_id'       => $progressReport->id,
                'table_name'    => 'far_complain_progress_reports'
            ]);

        } catch (\Exception $ex) {

            DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $farComplain
        ]);        
    }

    /**
     * Farmer complain get progress Report
     */
    public function getProgressReport($complain_id)
    {   
       
        $progressReport = FarmerComplainProgressReport::where('complain_id', $complain_id)->first();

        if(!$progressReport) {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        } 

        return response([
            'success'   => true,
            'data'      => $progressReport
        ]);
    }
}
