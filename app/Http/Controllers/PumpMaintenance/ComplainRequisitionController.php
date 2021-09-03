<?php

namespace App\Http\Controllers\PumpMaintenance;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpMaintenance\ComplainRequisitionValidations;
use App\Models\FarmerOperator\FarmerComplain;
use App\Models\PumpInstallation\PumpCurrentStocks;
use App\Models\PumpMaintenance\FarComplainReqApproval;
use App\Models\PumpMaintenance\FarComplainReqDetails;
use App\Models\PumpMaintenance\FarComplainRequistion;
use Exception;
use Illuminate\Http\Request;
use DB;

class ComplainRequisitionController extends Controller
{
    /**
     * get farmer complain requisition ID
     */
    public function getRequisitionId(Request $request)
    {
        $requisition = FarComplainRequistion::select('requisition_id')->orderBy('id','desc')->first();
        if ($requisition != null) {
            $requisition_id = $requisition['requisition_id'] + 1; 
            //str_pad($code, 4, 0, STR_PAD_LEFT)
        } else {
            $requisition_id = 200001;
        }
        return response([
            'success' => true,
            'message' => 'Requistion ID',
            'data'    => $requisition_id
        ]);
    }
    
    /**
     * get farmer complain requisition store
     */
    public function store(Request $request)
    {
        $validationResult = ComplainRequisitionValidations::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();
        
        try {
            
            $fcr                        = new FarComplainRequistion();
            $fcr->complain_id           = $request->complain_id;
            $fcr->org_id                = (int)$request->org_id;
            $fcr->office_id             = (int)$request->office_id;
            $fcr->pump_type_id          = (int)$request->pump_type_id;
            $fcr->requisition_id        = (int)$request->requisition_id;
            $fcr->remarks	            = $request->remarks;
            $fcr->remarks_bn	        = $request->remarks_bn;
            $fcr->id_serial	            = 0;
            $fcr->requisition_date	    = (new \DateTime($request->requisition_date))->format('Y-m-d');
            $fcr->created_by            = (int)user_id();
            $fcr->updated_by            = (int)user_id();
            $fcr->save();

            foreach($request->items as $item) {
                $fcr_detail                    = new FarComplainReqDetails();
                $fcr_detail->requisition_id    = (int)$fcr->id;
                $fcr_detail->item_id           = (int)$item['item_id'];
                $fcr_detail->quantity          = $item['quantity'];
                $fcr_detail->save();
            }

            DB::commit();

            save_log([
                'data_id'   => $fcr->id,
                'table_name'=> 'far_complain_requisitions'
            ]);

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
            'data'    => $fcr
        ]);
    }

    /**
     * get farmer complain requisition details
     */
    public function details($complain_id, $requisition_id)
    {  
        $requistion = FarComplainRequistion::leftjoin('far_complain_approvals','far_complain_approvals.requisition_id','far_complain_requisitions.id')
                        ->select('far_complain_requisitions.*','far_complain_approvals.receiver_id as receiver_id','far_complain_approvals.status as approval_status')
                        ->where('far_complain_requisitions.id', $requisition_id)
                        ->where('far_complain_requisitions.complain_id', $complain_id)
                        ->orderBy('far_complain_approvals.id','DESC')
                        ->first(); 

        $requistion_details = DB::table('far_complains')                                                                  
                    ->leftjoin('far_complain_requisitions','far_complains.id', '=', 'far_complain_requisitions.complain_id')                                                                    
                    ->leftjoin('far_complain_req_details','far_complain_requisitions.id', '=', 'far_complain_req_details.requisition_id')                                                                    
                    ->leftjoin('master_items','far_complain_req_details.item_id', '=', 'master_items.id')                                                                    
                    ->leftjoin('master_item_categories','master_items.category_id', '=', 'master_item_categories.id')   
                    ->leftjoin('pump_current_stocks','master_items.id', '=', 'pump_current_stocks.item_id')                                                                    
                    ->leftjoin('master_measurement_units','master_items.measurement_unit_id', '=','master_measurement_units.id')                                                                    
                    ->select('master_item_categories.category_name','master_item_categories.category_name_bn',
                            'master_items.item_name','master_items.item_name_bn',
                            'master_measurement_units.unit_name','master_measurement_units.unit_name_bn',
                            'far_complain_requisitions.id as requisition_id',
                            'pump_current_stocks.quantity as stock_quantity',
                            'far_complain_req_details.quantity as stock_out_quantity'
                        )
                    ->where('far_complain_requisitions.complain_id', $complain_id)  
                    ->where('far_complain_requisitions.id', $requisition_id)
                    ->get();  

        $requisition_edit = FarComplainReqDetails::where('requisition_id', $requisition_id)
                                                        ->where('accepted_quantity', '!=', 0)
                                                        ->count('id');

        $approval_notes = DB::table('far_complain_approvals')
                            ->select('sender_id','note','note_bn')
                            ->where('complain_id', $complain_id)
                            ->where('requisition_id', $requisition_id)
                            ->get();

        if(!$requistion) {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        } 

        return response([
            'success'   => true,
            'data' => [
                'requistion'        => $requistion,
                'requistion_details'=> $requistion_details,
                'requisition_edit'  => $requisition_edit,
                'approval_notes'    => $approval_notes
            ]
        ]);
    }

    /**
     * get farmer scheme receive requisition
     */
    public function receiveRequisitionList(Request $request)
    {  

        $query = DB::table('far_complains')
                    ->Join('far_basic_infos','far_complains.farmer_id', '=','far_basic_infos.farmer_id')
                    ->leftJoin('far_complain_progress_reports','far_complains.id', '=','far_complain_progress_reports.complain_id')
                    ->leftJoin('far_complain_requisitions','far_complains.id', '=','far_complain_requisitions.complain_id')
                    ->leftJoin('far_complain_supply_equipments','far_complain_requisitions.id', '=','far_complain_supply_equipments.requisition_id')
                    ->join('far_complain_approvals','far_complain_requisitions.id', '=','far_complain_approvals.requisition_id') 
                    ->select('far_complains.*','far_basic_infos.name_bn', 'far_basic_infos.name',
                        'far_complain_progress_reports.id as progress_report_id',
                        'far_complain_requisitions.id as complain_requisition_id',
                        'far_complain_requisitions.status as requisition_status',
                        'far_complain_supply_equipments.id as supply_equipment_id',
                        'far_complain_approvals.status as approval_status'
                    )
                    ->orderBy('far_complains.id','DESC')
                    ->where('far_complain_approvals.receiver_id', $request->office_id)
                    ->where('far_complains.status', '>=', 6)
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

        if ($request->subject) {
            $query = $query->where('far_complains.subject', 'like', "{$request->subject}%")
                        ->orWhere('far_complains.subject_bn', 'like', "{$request->subject}%");
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));            

        return response([
            'success' => true,
            'message' => 'Complain list',
            'data' => $list
        ]);
    }

    /**
     * receive requistion
     */
    public function receiveRequisition(Request $request) 
    { 
        DB::beginTransaction();
        try {
            $sra = FarComplainReqApproval::where('receiver_id', $request->receiver_id)
                                ->where('requisition_id', $request->requisition_id)
                                ->where('complain_id', $request->complain_id)
                                ->first();

            if ($sra) {
                
                $sra->status = 2;
                $sra->update();

                save_log([
                    'data_id'       => $sra->id,
                    'table_name'    => 'far_complain_approvals',
                    'execution_type'=> 1
                ]);

                $fcr = FarComplainRequistion::find($request->requisition_id);
                $fcr->status = 2;
                $fcr->update;

                save_log([
                    'data_id'       => $fcr->id,
                    'table_name'    => 'far_complain_requisitions',
                    'execution_type'=> 1
                ]);
            }

            DB::commit();
            
        } catch (Exception $ex) {

            DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully'
        ]);
    }

    /**
     * farmer complain requisition quantity update
     */
    public function quantityUpdate(Request $request)
    {  
        DB::beginTransaction();

        try {

            $fcr = FarComplainRequistion::find($request[0]['requisition_id']);
            $fcr->status = 6;
            $fcr->update();

            save_log([
                'data_id'   => $fcr->id,
                'table_name'=> 'far_complain_requisitions'
            ]);
            
            foreach($request->all() as $item){

                $requistion_detail = FarComplainReqDetails::where('requisition_id', $item['requisition_id'])->first();
                $requistion_detail->accepted_quantity = $item['accept_quantity'];
                $requistion_detail->update();
            }

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
            'message' => 'Data update successfully',
            'data'    => []
        ]);

    }

    /**
     * farmer complain requisition approve
     */
    public function approve(Request $request)
    {  
        DB::beginTransaction();
        
        try {
        
            $fc         = FarmerComplain::find($request->complain_id);
            $fc->status = $request->status; // status 6 = requision, mean complain requisition done, wait for supply equipment
            $fc->update();

            save_log([
                'data_id'   => $fc->id,
                'table_name'=> 'far_complains',
                'execution_type' => 1
            ]);
            
            $fcr           = FarComplainRequistion::find($request->requisition_id);
            $fcr->status   = 3; // 3 mean requisition approve
            $fcr->update();

            save_log([
                'data_id'   => $fcr->id,
                'table_name'=> 'far_complain_requisitions',
                'execution_type' => 1
            ]);

            $current_stocks = FarComplainReqDetails::join('far_complain_requisitions','far_complain_requisitions.id','far_complain_req_details.requisition_id')
                                            ->select('far_complain_req_details.requisition_id','far_complain_req_details.item_id','far_complain_req_details.quantity')
                                            ->where('far_complain_req_details.requisition_id', $request->requisition_id)
                                            // ->where('far_complain_requisitions.office_id', $request->office_id)
                                            ->where('far_complain_requisitions.org_id', $request->org_id)
                                            ->get();

            if ($current_stocks->count() > 0) {

                foreach ($current_stocks as $stock) {

                    $stock_update = PumpCurrentStocks::where('item_id', $stock['item_id'])->first();
                    $stock_update->quantity =($stock_update->quantity - $stock['quantity']);
                    $stock_update->update();

                }
            }

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
            'message' => 'Data update successfully',
            'data'    => $fc
        ]);

    }

    /**
     * farmer complain requisition forward or allocate
     */
    public function forwardAllocate(Request $request)
    {         
        DB::beginTransaction();

        try {
       
            $fcra                   = new FarComplainReqApproval();
            $fcra->complain_id      = $request->complain_id;
            $fcra->requisition_id   = $request->requisition_id;
            $fcra->sender_id        = $request->sender_id;
            $fcra->receiver_id      = $request->receiver_id;
            $fcra->note             = $request->note;
            $fcra->note_bn          = $request->note_bn;
            $fcra->save();

            save_log([
                'data_id'   => $fcra->id,
                'table_name'=> 'far_complain_approvals'
            ]);

            $fcr = FarComplainRequistion::find($request->requisition_id);
            $fcr->status = $request->forward_type == 1 ? 4 : 5;
            $fcr->update();

            save_log([
                'data_id'   => $fcr->id,
                'table_name'=> 'far_complain_requisitions',
                'execution_type' => 1
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
            'message' => 'Data update successfully',
            'data'    => $fcra
        ]);

    }
}
