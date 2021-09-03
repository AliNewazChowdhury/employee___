<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\PumpStockOutInfosValidations;
use App\Models\PumpInstallation\PumpStockOutInfos;
use App\Models\PumpInstallation\PumpStockOutDetails;
use App\Models\PumpInstallation\PumpCurrentStocks;
use Illuminate\Http\Request;
use DB;

class PumpStockOutInfosController extends Controller
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
     * get all Pump Stock In Infos
     */
    public function index(Request $request)
    {
        $query = DB::table('pump_stock_out_infos')->select('pump_stock_out_infos.*');
                 
        if ($request->stock_out_id) {
            $query = $query->where('pump_stock_out_infos.stock_out_id', $request->stock_out_id);
        }
        if ($request->org_id) {
            $query = $query->where('pump_stock_out_infos.org_id', $request->org_id);
        }
        if ($request->office_id) {
            $query = $query->where('pump_stock_out_infos.office_id', $request->office_id);
        }

        if ($request->stock_out_date) {
            $query = $query->where('pump_stock_out_infos.stock_out_date', date('Y-m-d', strtotime($request->stock_out_date)));
        }

        if ($request->status) {
            $query = $query->where('pump_stock_out_infos.status', $request->status);
        }

        if ($request->from_date) {
            $query = $query->where('pump_stock_out_infos.stock_out_date', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->to_date) {
            $query = $query->where('pump_stock_out_infos.stock_out_date', '<=', date('Y-m-d', strtotime($request->to_date)));
        }        

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Stock out list',
            'data'    => $list
        ]);
    }

    public function single_index(Request $request, $id)
    {        
        $query = DB::table('pump_stock_out_details');                          
         
        $query = $query
            ->join('master_items','pump_stock_out_details.item_id', '=','master_items.id')
            ->join('master_item_categories','master_items.category_id', '=','master_item_categories.id')
            ->join('master_measurement_units','master_items.measurement_unit_id', '=','master_measurement_units.id');

        $query =  $query->select('pump_stock_out_details.*',
                'master_items.id as item_id',
                'master_items.item_name',
                'master_items.item_name_bn',
                'master_item_categories.id as item_cat_id',
                'master_item_categories.category_name',
                'master_item_categories.category_name_bn',
                'master_measurement_units.unit_name',
                'master_measurement_units.unit_name_bn'
            );  
        

        $query = $query->where('pump_stock_out_details.id', $id);


        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Pump stock out infos list',
            'data' => $list
        ]);
    }

    /**
     * Pump Stock Out Infos store
     */
    public function store(Request $request)
    {
       // $validationResult = PumpStockOutInfosValidations:: validate($request);    
        
        // if (!$validationResult['success']) {
        //     return response($validationResult);
        // }

        $LastIdSerialGet = DB::table('pump_stock_out_infos')
                ->latest()
                ->select('id_serial')
                ->orderBy('id','desc')
                ->first();

        if($LastIdSerialGet){
            $id_serial = $LastIdSerialGet->id_serial;
        
            if( $id_serial !="" ){
                $id_serial+= 1;
            }

        }else{
            $id_serial = 100000;
        }

        $stock_out_id    = "STOCKOUT".$id_serial;        
        $org_id         = $request->org_id;
        $office_id      = $request->office_id;

        DB::beginTransaction();

        try {

            $PumpStockOutInfos                     = new PumpStockOutInfos();
            $PumpStockOutInfos->stock_out_id        = $stock_out_id;
            $PumpStockOutInfos->org_id             = (int) $org_id;
            $PumpStockOutInfos->office_id          = (int) $office_id;
            $PumpStockOutInfos->id_serial          = $id_serial;
            $PumpStockOutInfos->stock_out_date     = $request->stock_out_date;
            $PumpStockOutInfos->reason             = $request->reason;
            $PumpStockOutInfos->reason_bn          = $request->reason_bn;
            $PumpStockOutInfos->purpose            = $request->purpose;
            $PumpStockOutInfos->purpose_bn         = $request->purpose_bn;
            $PumpStockOutInfos->remarks            = $request->remarks;
            $PumpStockOutInfos->remarks_bn         = $request->remarks_bn;
            $PumpStockOutInfos->created_by         = (int)user_id();
            $PumpStockOutInfos->updated_by         = (int)user_id();

            if( $PumpStockOutInfos->save() ){
                $stock_out_infos_id = $PumpStockOutInfos->id;
                $items = $request->items;
                $quantity = $request->quantity;                

                foreach ($items as $value) {
                    $PumpStockOutDetails                       = new PumpStockOutDetails();
                    $PumpStockOutDetails->stock_out_infos_id    = (int) $stock_out_infos_id;
                    $PumpStockOutDetails->item_id              =  $value['item_id'];
                    $PumpStockOutDetails->quantity             =  $value['quantity'];  
                    $PumpStockOutDetails->save();                    
                }

            }

            DB::commit();

            save_log([
                'data_id'    => $PumpStockOutInfos->id,
                'table_name' => 'pump_stock_out_infos'
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $PumpStockOutInfos
        ]);
    }

    public function pumpStockOutApprovel($id)
    {
        $PumpStockOutInfos = PumpStockOutInfos::find($id);

        if (!$PumpStockOutInfos) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try { 

            $org_id     = $PumpStockOutInfos->org_id;
            $office_id  = $PumpStockOutInfos->office_id;

            $PumpStockOutInfos->status = 1;

            if( $PumpStockOutInfos->save() && $PumpStockOutInfos->status == 1){
                $PumpStockOutDetails = PumpStockOutDetails::where('stock_out_infos_id', $id)->get(); 

                foreach ($PumpStockOutDetails as $value) {

                     $PreviousStocks = PumpCurrentStocks::where('org_id', $org_id)
                                                                ->where('office_id', $office_id)
                                                                ->where('item_id', $value->item_id)
                                                                ->first(); 

                    $stockOutquantity   = $value->quantity;

                    if($PreviousStocks){
                        $previous_quantity  = $PreviousStocks->quantity;

                        if( $previous_quantity >= $stockOutquantity){
                            $current_quantity                = $previous_quantity - $stockOutquantity;

                            $PreviousStocks->org_id          = $org_id;
                            $PreviousStocks->office_id       = $office_id;
                            $PreviousStocks->item_id         = $value->item_id;
                            $PreviousStocks->quantity        = $current_quantity;
                            $PreviousStocks->updated_by      = (int)user_id();
                            $PreviousStocks->save();

                        }else{
                            return response([
                                'success' => false,
                                'message' => 'Do not have enough stock amount.'
                            ]);
                        }

                    }else{
                        return response([
                            'success' => false,
                            'message' => 'Do not have any stock at the moment.'
                        ]);
                    }                    
                }
            }

            DB::commit();
            
            save_log([
                'data_id'       => $id,
                'table_name'    => 'pump_stock_out_details',
                'execution_type'=> 2
            ]);


        }catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Data not update'
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $PumpStockOutDetails
        ]);
    }

    
}
