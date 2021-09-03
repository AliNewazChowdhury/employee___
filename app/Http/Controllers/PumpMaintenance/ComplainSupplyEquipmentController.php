<?php

namespace App\Http\Controllers\PumpMaintenance;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpMaintenance\ComplainSupplyEquipmentValidation;
use App\Models\FarmerOperator\FarmerComplain;
use App\Models\PumpMaintenance\FarComplainReqSupplyEquipment;
use Illuminate\Http\Request;
use DB;

class ComplainSupplyEquipmentController extends Controller
{
     /**
     * Farmer complain maintenace task list
     * where complain status = 6 
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
                        'far_complain_supply_equipments.id as supply_note_id'
                    )
                    ->orderBy('far_complains.id','DESC')
                    ->where(function($q){
                        return $q->where('far_complains.status', 6)
                                ->orWhere('far_complains.status', 7);
                    })
                    ->where('far_complain_requisitions.id','!=', null);                    
        
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
     * farmer complain supply equipment store
     */
    public function store(Request $request)
    {
        $validationResult = ComplainSupplyEquipmentValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }
        
        try {
            
            $fcse                    = new FarComplainReqSupplyEquipment();
            $fcse->supply_note       = $request->supply_note;
            $fcse->supply_note_bn    = $request->supply_note_bn;
            $fcse->supply_date	     = (new \DateTime($request->supply_date))->format('Y-m-d');
            $fcse->requisition_id    = (int)$request->requisition_id;
            $fcse->created_by        = (int)user_id();
            $fcse->updated_by        = (int)user_id();
            $fcse->save();

            save_log([
                'data_id'   => $fcse->id,
                'table_name'=> 'far_complain_supply_equipments'
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
            'data'    => $fcse
        ]);
    }

    /**
     * get farmer complain supply equipment details
     */
    public function details($requisition_id)
    { 
        $supply_equipment = FarComplainReqSupplyEquipment::where('requisition_id', $requisition_id)->first(); 

        if(!$supply_equipment) {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        } 

        return response([
            'success'   => true,
            'data'      => $supply_equipment
        ]);
    }
}
