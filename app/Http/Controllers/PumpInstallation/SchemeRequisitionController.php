<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\SchemeRequisitionValidation;
use App\Models\PumpInstallation\SchemeRequisition;
use App\Models\PumpInstallation\SchemeRequisitionDetails;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\PumpCurrentStocks;
use App\Models\PumpInstallation\SchemeRequistionApproval;
use Illuminate\Http\Request;
use DB;
use Exception;

class SchemeRequisitionController extends Controller
{
    /**
     * get farmer Scheme Requisition ID
     */
    public function getRequisitionId(Request $request)
    {
        $requisition = SchemeRequisition::select('requisition_id')->orderBy('id','desc')->first();
        if ($requisition != null) {
            $requisition_id = $requisition['requisition_id'] + 1; 
            //str_pad($code, 4, 0, STR_PAD_LEFT)
        } else {
            $requisition_id = 100001;
        }
        return response([
            'success' => true,
            'message' => 'Requistion ID',
            'data'    => $requisition_id
        ]);
    }
    
    /**
     * get farmer scheme requisition store
     */
    public function store(Request $request)
    {
        $validationResult = SchemeRequisitionValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();
        
        try {
            
            $sch_req                        = new SchemeRequisition();
            $sch_req->org_id                = (int)$request->org_id;
            $sch_req->office_id             = (int)$request->office_id;
            $sch_req->pump_type_id          = (int)$request->pump_type_id;
            $sch_req->requisition_id        = (int)$request->requisition_id;
            $sch_req->remarks	            = $request->remarks;
            $sch_req->remarks_bn	        = $request->remarks_bn;
            $sch_req->requisition_date	    = (new \DateTime($request->requisition_date))->format('Y-m-d');
            $sch_req->id_serial	            = (int)$request->id_serial;
            $sch_req->scheme_application_id = (int)$request->scheme_application_id;
            $sch_req->created_by            = (int)user_id();
            $sch_req->updated_by            = (int)user_id();
            $sch_req->save();

            foreach($request->items as $item) {
                $sche_req_detail                    = new SchemeRequisitionDetails();
                $sche_req_detail->requisition_id    = (int)$sch_req->id;
                $sche_req_detail->item_id           = (int)$item['item_id'];
                $sche_req_detail->quantity          = $item['quantity'];
                $sche_req_detail->save();
            }

            DB::commit();

            save_log([
                'data_id'   => $sch_req->id,
                'table_name'=> 'far_scheme_requisitions'
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
            'data'    => $sch_req
        ]);
    }

    /**
     * get farmer scheme requisition details
     */
    public function details($scheme_application_id, $requisition_id)
    {   
        $requistion = SchemeRequisition::leftjoin('far_requisition_approvals','far_requisition_approvals.requisition_id','far_scheme_requisitions.id')
                        ->select('far_scheme_requisitions.*','far_requisition_approvals.receiver_id as receiver_id','far_requisition_approvals.status as approval_status')
                        ->where('far_scheme_requisitions.id', $requisition_id)
                        ->where('far_scheme_requisitions.scheme_application_id', $scheme_application_id)
                        ->orderBy('far_requisition_approvals.id', 'DESC')
                        ->first(); 

        $requistion_details = DB::table('far_scheme_requisitions')    
                    ->join('far_scheme_req_details','far_scheme_requisitions.id', '=','far_scheme_req_details.requisition_id')                                                                    
                    ->join('master_items','far_scheme_req_details.item_id', '=','master_items.id')                                                                    
                    ->join('master_item_categories','master_items.category_id', '=','master_item_categories.id')                                                                                       
                    ->join('pump_current_stocks','master_items.id', '=','pump_current_stocks.item_id')                                                                    
                    ->join('master_measurement_units','master_items.measurement_unit_id', '=','master_measurement_units.id')                                                                       
                    ->select('master_item_categories.category_name','master_item_categories.category_name_bn',
                            'master_items.item_name','master_items.item_name_bn',
                            'master_measurement_units.unit_name','master_measurement_units.unit_name_bn',
                            'far_scheme_requisitions.id as requisition_id',
                            'pump_current_stocks.quantity as stock_quantity',
                            'far_scheme_req_details.quantity as stock_out_quantity'
                        )
                    ->where('far_scheme_requisitions.scheme_application_id', $scheme_application_id)  
                    ->where('far_scheme_requisitions.id', $requisition_id)
                    ->get();  

        $requisition_edit = SchemeRequisitionDetails::where('requisition_id', $requisition_id)
                                                        ->where('accepted_quantity', '!=',0)
                                                        ->count('id');

        $approval_notes = DB::table('far_requisition_approvals')
                            ->select('sender_id','note','note_bn')
                            ->where('scheme_application_id', $scheme_application_id)
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
        $query = DB::table('far_scheme_application')                          
                        ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')                         
                        ->leftjoin('far_scheme_license','far_scheme_application.id', '=','far_scheme_license.scheme_application_id')                                                
                        ->leftjoin('far_scheme_requisitions','far_scheme_application.id', '=','far_scheme_requisitions.scheme_application_id')                                                
                        ->join('far_requisition_approvals','far_scheme_requisitions.id', '=','far_requisition_approvals.requisition_id') 
                        ->select('far_scheme_application.*',                            
                                'far_scheme_app_details.id as details.id',
                                'far_scheme_app_details.farmer_id as details.farmer_id',
                                'far_scheme_app_details.email as details.email',
                                'far_scheme_app_details.scheme_application_id',
                                'far_scheme_app_details.sch_man_name',
                                'far_scheme_app_details.sch_man_name_bn',
                                'far_scheme_app_details.sch_man_father_name',
                                'far_scheme_app_details.sch_man_father_name_bn',
                                'far_scheme_app_details.sch_man_mother_name',
                                'far_scheme_app_details.sch_man_mother_name_bn',
                                'far_scheme_app_details.sch_man_division_id',
                                'far_scheme_app_details.sch_man_district_id',
                                'far_scheme_app_details.sch_man_upazilla_id',
                                'far_scheme_app_details.sch_man_union_id',
                                'far_scheme_app_details.sch_man_village',
                                'far_scheme_app_details.sch_man_village_bn',
                                'far_scheme_app_details.sch_man_mobile_no',
                                'far_scheme_app_details.sch_man_nid',
                                'far_scheme_app_details.pump_division_id',
                                'far_scheme_app_details.pump_district_id',
                                'far_scheme_app_details.pump_upazilla_id',
                                'far_scheme_app_details.pump_union_id',
                                'far_scheme_app_details.pump_mauza_no',
                                'far_scheme_app_details.pump_mauza_no_bn',
                                'far_scheme_app_details.pump_jl_no',
                                'far_scheme_app_details.pump_jl_no_bn',
                                'far_scheme_app_details.pump_plot_no',
                                'far_scheme_app_details.pump_plot_no_bn',
                                'far_scheme_app_details.scheme_lands',
                                'far_scheme_app_details.general_minutes',
                                'far_scheme_app_details.scheme_map',
                                'far_scheme_app_details.affidavit_id',
                                'far_scheme_license.license_no',
                                'far_scheme_requisitions.id as requisition_id',
                                'far_scheme_requisitions.status as requisition_status',
                                'far_requisition_approvals.status as approval_status'
                            )
                        ->where('far_scheme_application.status', 8)
                        ->where('far_requisition_approvals.receiver_id', $request->office_id); 

        if ($request->name) {
            $query = $query->where('far_scheme_application.name', 'like', "{$request->name}%")
                            ->orWhere('name_bn', 'like', "{$request->name}%");
        }

        if ($request->scheme_type_id) {
            $query = $query->where('far_scheme_application.scheme_type_id', $request->scheme_type_id);
        }

        if ($request->org_id) {
            $query = $query->where('far_scheme_application.org_id', $request->org_id);
        }

        if ($request->application_id) {
            $query = $query->where('far_scheme_application.application_id', $request->application_id);
        }
        
        if ($request->far_district_id) {
            $query = $query->where('far_scheme_application.far_district_id', $request->far_district_id);
        }             

        if ($request->far_upazilla_id) {
            $query = $query->where('far_scheme_application.far_upazilla_id', $request->far_upazilla_id);
        }
        
        if ($request->far_union_id) {
            $query = $query->where('far_scheme_application.far_union_id', $request->far_union_id);
        }       

        if ($request->far_mobile_no) {
            $query = $query->where('far_scheme_application.far_mobile_no', $request->far_mobile_no);
        }

        if ($request->farmer_id) {
                $query = $query->where('far_scheme_application.farmer_id', $request->farmer_id);
        }
            
        if ($request->far_division_id) {
            $query = $query->where('far_scheme_application.far_division_id', $request->far_division_id);
        }  

        if ($request->email) {
            $query = $query->where('far_scheme_application.email', $request->email);
        } 

        if ($request->father_name) {
            $query = $query->where('far_scheme_application.father_name', 'like', "{$request->father_name}%")
                            ->orWhere('father_name_bn', 'like', "{$request->father_name}%");
        } 

        if ($request->mother_name) {
            $query = $query->where('far_scheme_application.mother_name', 'like', "{$request->mother_name}%")
                            ->orWhere('mother_name_bn', 'like', "{$request->mother_name}%");
        }

        if ($request->nid) {
            $query = $query->where('far_scheme_application.nid', $request->nid);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));            

        return response([
            'success' => true,
            'message' => 'Application list',
            'data'    => $list
        ]);
    }

    /**
     * receive requistion
     */
    public function receiveRequisition(Request $request) 
    {  
        DB::beginTransaction();
        try {
            $reqApproval = SchemeRequistionApproval::where('receiver_id', $request->receiver_id)
                                ->where('requisition_id', $request->requisition_id)
                                ->where('scheme_application_id', $request->scheme_application_id)
                                ->first();

            if ($reqApproval) {
                
                $reqApproval->status = 2;
                $reqApproval->update();

                save_log([
                    'data_id'       => $reqApproval->id,
                    'table_name'    => 'far_requisition_approvals',
                    'execution_type'=> 1
                ]);

                $scheReq = SchemeRequisition::find($request->requisition_id);
                $scheReq->status = 2;
                $scheReq->update;

                save_log([
                    'data_id'       => $scheReq->id,
                    'table_name'    => 'far_scheme_requisitions',
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
     * farmer scheme requisition quantity update
     */
    public function quantityUpdate(Request $request)
    {  
        DB::beginTransaction();
        try {

            $schmeRequisition = SchemeRequisition::find($request->requisition_id);
            $schmeRequisition->status = 6;
            $schmeRequisition->update();

            save_log([
                'data_id'   => $schmeRequisition->id,
                'table_name'=> 'far_scheme_requisitions'
            ]);
            
            foreach($request->all() as $item){
                $requistion_detail = SchemeRequisitionDetails::where('requisition_id', $item['requisition_id'])->first();
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
     * farmer scheme requisition approve
     */
    public function approve(Request $request)
    {  
        DB::beginTransaction();
        
        try {
        
            $farmer_sch_app         = FarmerSchemeApplication::find($request->scheme_application_id);
            $farmer_sch_app->status = $request->status;
            $farmer_sch_app->update();

            save_log([
                'data_id'   => $farmer_sch_app->id,
                'table_name'=> 'far_scheme_application',
                'execution_type' => 1
            ]);
        
            $scheReqApp = SchemeRequistionApproval::where('requisition_id', $request->requisition_id)->orderBy('id','DESC')->first();
            if ($scheReqApp != null)  {
                $scheReqApp->status = 2; // 2 mean approve
                $scheReqApp->update();

                save_log([
                    'data_id'   => $scheReqApp->id,
                    'table_name'=> 'far_requisition_approvals',
                    'execution_type' => 1
                ]);
            }   
            
            $sche_req           = SchemeRequisition::find($request->requisition_id);
            $sche_req->status   = 3;
            $sche_req->update();

            save_log([
                'data_id'   => $sche_req->id,
                'table_name'=> 'far_scheme_requisitions',
                'execution_type' => 1
            ]);

            $current_stocks = SchemeRequisitionDetails::join('far_scheme_requisitions','far_scheme_requisitions.id','far_scheme_req_details.requisition_id')
                                            ->select('far_scheme_req_details.requisition_id','far_scheme_req_details.item_id','far_scheme_req_details.quantity')
                                            ->where('far_scheme_req_details.requisition_id', $request->requisition_id)
                                            ->where('far_scheme_requisitions.office_id', $request->office_id)
                                            ->where('far_scheme_requisitions.org_id', $request->org_id)
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
            'data'    => $farmer_sch_app
        ]);

    }

    /**
     * farmer scheme requisition forward or allocate
     */
    public function forwardAllocate(Request $request)
    {         
        DB::beginTransaction();

        try {
       
            $scheReqApp     = new SchemeRequistionApproval();
            $scheReqApp->scheme_application_id  = $request->scheme_application_id;
            $scheReqApp->requisition_id         = $request->requisition_id;
            $scheReqApp->sender_id              = $request->sender_id;
            $scheReqApp->receiver_id            = $request->receiver_id;
            $scheReqApp->note                   = $request->note;
            $scheReqApp->note_bn                = $request->note_bn;
            $scheReqApp->save();

            save_log([
                'data_id'   => $scheReqApp->id,
                'table_name'=> 'far_requisition_approvals'
            ]);

            $schmeRequisition = SchemeRequisition::find($request->requisition_id);
            $schmeRequisition->status = $request->forward_type == 1 ? 4 : 5;
            $schmeRequisition->update();

            save_log([
                'data_id'   => $schmeRequisition->id,
                'table_name'=> 'far_scheme_requisitions',
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
            'data'    => $scheReqApp
        ]);

    }
}
