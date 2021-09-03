<?php

namespace App\Http\Controllers\PumpMaintenance;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpMaintenance\TroubleEquipmentValidation;
use App\Models\FarmerOperator\FarmerComplain;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInfoManagement\PumpInfo;
use App\Models\PumpMaintenance\ComplainResolved;
use App\Models\PumpMaintenance\FarmerComplainTroubleEquipment;
use App\Models\PumpMaintenance\FarmerComplainTroubleEquipmentDetail;
use App\Models\PumpMaintenance\Resunk;
use Illuminate\Http\Request;
use DB;

class RequiredMaintenanceController extends Controller
{
     /**
     * Farmer complain maintenace list
     * where complain status = 4 
     */
    public function index(Request $request)
    {   
        $query = DB::table('far_complains')
                    ->Join('far_basic_infos','far_complains.farmer_id', '=','far_basic_infos.farmer_id')
                    ->leftjoin('far_resunks','far_complains.id', '=','far_resunks.complain_id')
                    ->leftjoin('far_complain_tro_equipments','far_complains.id', '=','far_complain_tro_equipments.complain_id')
                    ->select('far_complains.*','far_basic_infos.name_bn', 'far_basic_infos.name','far_complain_tro_equipments.id as tro_equipment_id','far_resunks.id as resunk_id')
                    ->orderBy('far_complains.id','DESC')
                    ->where('far_complains.status', 4);
        
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
            'message' => 'Complain maintenance list',
            'data' => $list
        ]);
        
    }

    /**
     * Farmer complain resolved in required maintenance
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
     * Pump details with division_id, district_id, upazilla_id & union_id
     */
    public function pumpDetails(Request $request)
    {   
        $pumpInfo = DB::table('far_scheme_application')
                    ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id')                                                 
                    ->select('far_scheme_application.pump_id','far_scheme_app_details.pump_mauza_no', 'far_scheme_app_details.pump_jl_no','far_scheme_app_details.pump_plot_no')
                    ->where('far_scheme_app_details.farmer_id', $request->farmer_id)
                    ->where('far_scheme_app_details.pump_division_id', $request->division_id)
                    ->where('far_scheme_app_details.pump_district_id', $request->district_id)
                    ->where('far_scheme_app_details.pump_upazilla_id', $request->upazilla_id)
                    ->where('far_scheme_app_details.pump_union_id', $request->union_id)
                    ->first(); 
        
        if(!$pumpInfo) {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data found',
            'data'    => $pumpInfo
        ]);        
    }   

    /**
     * Pump maintenance trouble equipment store
     */
    public function troubleEquipmentStore(Request $request)
    {   
        $validationResult = TroubleEquipmentValidation:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();

        try {

            $farCompTroEquip = new FarmerComplainTroubleEquipment();
            $farCompTroEquip->complain_id   = $request->complain_id;
            $farCompTroEquip->division_id   = $request->division_id;
            $farCompTroEquip->district_id   = $request->district_id;
            $farCompTroEquip->upazilla_id   = $request->upazilla_id;
            $farCompTroEquip->union_id      = $request->union_id;
            $farCompTroEquip->mauza_no      = $request->mauza_no;
            $farCompTroEquip->jl_no         = $request->jl_no;
            $farCompTroEquip->plot_no       = $request->plot_no;
            $farCompTroEquip->created_by    = (int)user_id();
            $farCompTroEquip->updated_by    = (int)user_id();
            $farCompTroEquip->save();

            foreach($request->details as $detail) {
                $farCompTroEquipDetail = new FarmerComplainTroubleEquipmentDetail();
                $farCompTroEquipDetail->tro_equipments_id = $farCompTroEquip->id;
                $farCompTroEquipDetail->name        = $detail['name'];
                $farCompTroEquipDetail->name_bn     = $detail['name_bn'];
                $farCompTroEquipDetail->note        = $detail['note'];
                $farCompTroEquipDetail->note_bn     = $detail['note_bn'];
                $farCompTroEquipDetail->save();
            }
            
            DB::commit();

            save_log([
                'data_id'       => $farCompTroEquip->id,
                'table_name'    => 'far_complain_tro_equipments'
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
            'data'    => $farCompTroEquip
        ]);   
    }   

    /**
     * Pump maintenance trouble equipment Details
     */
    public function troubleEquipmentDetails($complain_id)
    {   
        $troubleEquipment = FarmerComplainTroubleEquipment::where('complain_id', $complain_id)->first();
        $troubleEquipmentDetails = DB::table('far_complain_tro_equipments')
                                ->leftjoin('far_complain_tro_equipment_details','far_complain_tro_equipments.id','far_complain_tro_equipment_details.tro_equipments_id')
                                ->select('far_complain_tro_equipment_details.*')
                                ->where('far_complain_tro_equipments.complain_id', $complain_id)
                                ->get();

        if (!$troubleEquipment && ! $troubleEquipmentDetails) {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);  
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => [
                'troubleEquipment'  => $troubleEquipment,
                'troubleEquipmentDetails'  => $troubleEquipmentDetails
            ]
        ]);  
    }   

    /**
     * Pump required maintenance pump List
     */
    public function pumpList(Request $request)
    {   
        $pumpList = PumpInfo::select('id as value','pump_id as text')
                            ->where('division_id', $request->division_id)
                            ->where('district_id', $request->district_id)
                            ->where('upazilla_id', $request->upazilla_id)
                            ->where('status', 0)
                            ->get();

        if ($pumpList->count() > 0) {

            return response([
                'success' => true,
                'data'    => $pumpList
            ]);  

        } else {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]); 
        }
    }   

    /**
     * Farmer complain resunk
     */
    public function resunk(Request $request)
    {   
        DB::beginTransaction();

        try {

            $resunk = new Resunk();
            $resunk->complain_id            = $request->complain_id;
            $resunk->pump_informations_id   = $request->pump_informations_id;
            $resunk->resunk_note            = $request->resunk_note;
            $resunk->resunk_note_bn         = $request->resunk_note_bn;
            $resunk->save();

            $farComplain = FarmerComplain::find($request->complain_id);
            $farComplain->status = 8; //8 mean resunk
            $farComplain->update();

            DB::commit();
            
            save_log([
                'data_id'       => $resunk->id,
                'table_name'    => 'far_resunks'
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
     * Farmer complain send to maintenance task
     */
    public function send(Request $request)
    {   
        try {

            $farComplain = FarmerComplain::find($request->complain_id);
            $farComplain->status = 5;
            $farComplain->update();
            
            save_log([
                'data_id'       => $farComplain->id,
                'table_name'    => 'far_complains',
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
            'message' => 'Data save successfully',
            'data'    => $farComplain
        ]);        
    }   
}
