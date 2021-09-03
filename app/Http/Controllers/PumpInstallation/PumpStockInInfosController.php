<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\PumpStockInInfosValidations;
use App\Models\PumpInstallation\PumpStockInInfos;
use App\Models\PumpInstallation\PumpStockInDetails;
use App\Models\PumpInstallation\PumpCurrentStocks;
use Illuminate\Http\Request;
use DB;

class PumpStockInInfosController extends Controller
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
        $query = DB::table('pump_stock_in_infos');
        $query =  $query->select('pump_stock_in_infos.*');

        if ($request->stock_in_id) {
            $query = $query->where('pump_stock_in_infos.stock_in_id', $request->stock_in_id);
        }
        if ($request->org_id) {
            $query = $query->where('pump_stock_in_infos.org_id', $request->org_id);
        }
        if ($request->office_id) {
            $query = $query->where('pump_stock_in_infos.office_id', $request->office_id);
        }

        if ($request->stock_date) {
            $query = $query->where('pump_stock_in_infos.stock_date', $request->stock_date);
        }

        if ($request->stock_in_infos_id) {
            $query = $query->where('pump_stock_in_infos.id', $request->stock_in_infos_id);
        }
        if ($request->status) {
            $query = $query->where('pump_stock_in_infos.status', $request->status);
        }

        if ($request->from_date) {
            $query = $query->whereDate('pump_stock_in_infos.created_at', '>=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->to_date) {
            $query = $query->whereDate('pump_stock_in_infos.created_at', '<=', date('Y-m-d', strtotime($request->to_date)));
        }



        $list = $query->paginate($request->per_page);
        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }

    public function single_index(Request $request, $id)
    {

        $query = DB::table('pump_stock_in_details');

        $query = $query
            ->join('master_items','pump_stock_in_details.item_id', '=','master_items.id')
            ->join('master_item_categories','master_items.category_id', '=','master_item_categories.id')
            ->join('master_measurement_units','master_items.measurement_unit_id', '=','master_measurement_units.id');

        $query =  $query->select('pump_stock_in_details.*',
                'master_items.id as item_id',
                'master_items.item_name',
                'master_items.item_name_bn',
                'master_item_categories.id as item_cat_id',
                'master_item_categories.category_name',
                'master_item_categories.category_name_bn',
                'master_measurement_units.unit_name',
                'master_measurement_units.unit_name_bn'
            );

        $query = $query->where('pump_stock_in_details.stock_in_infos_id', $id);

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Notification Setting list',
            'data' => $list
        ]);
    }

    /**
     * Pump Stock In Infos store
     */
    public function store(Request $request)
    {
       // $validationResult = PumpStockInInfosValidations:: validate($request);

        // if (!$validationResult['success']) {
        //     return response($validationResult);
        // }

        $LastIdSerialGet = DB::table('pump_stock_in_infos')
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

        $stock_in_id    = "STO".$id_serial;
        $org_id         = $request->org_id;
        $office_id      = $request->office_id;


        DB::beginTransaction();

        try {

            $PumpStockInInfos                     = new PumpStockInInfos();
            $PumpStockInInfos->stock_in_id        = $stock_in_id;
            $PumpStockInInfos->org_id             = (int) $org_id;
            $PumpStockInInfos->office_id          = (int) $office_id;
            $PumpStockInInfos->id_serial          = $id_serial;
            $PumpStockInInfos->stock_date          = $request->stock_date;
            $PumpStockInInfos->created_by         = (int)user_id();
            $PumpStockInInfos->updated_by         = (int)user_id();

            if( $PumpStockInInfos->save() ){
                $stock_in_infos_id = $PumpStockInInfos->id;
                $items = $request->items;
                $quantity = $request->quantity;

                foreach ($items as $value) {
                    $PumpStockInDetails                       = new PumpStockInDetails();
                    $PumpStockInDetails->stock_in_infos_id    = (int) $stock_in_infos_id;
                    $PumpStockInDetails->item_id              =  $value['item_id'];
                    $PumpStockInDetails->quantity             =  $value['quantity'];

                    $PreviousStocks = PumpCurrentStocks::where('org_id', $org_id)
                                                            ->where('office_id', $office_id)
                                                            ->where('item_id', $value['item_id'])
                                                            ->first();
                    $ad_new_quantity = (int) $value['quantity'];




                    if($PreviousStocks){

                        $previous_quantity  = $PreviousStocks->quantity;
                        $current_quantity  = $previous_quantity + $ad_new_quantity;

                        if($PumpStockInDetails->save()){

                            $PreviousStocks->org_id          = $org_id;
                            $PreviousStocks->office_id       = $office_id;
                            $PreviousStocks->item_id         = $value['item_id'];
                            $PreviousStocks->quantity        = $current_quantity;
                            $PreviousStocks->updated_by      = (int)user_id();
                            $PreviousStocks->save();
                        }


                    }else{

                        if($PumpStockInDetails->save()){
                            $PumpCurrentStocks = new PumpCurrentStocks();

                            $current_quantity  = $ad_new_quantity;
                            $PumpCurrentStocks->org_id          = $org_id;
                            $PumpCurrentStocks->office_id       = $office_id;
                            $PumpCurrentStocks->item_id         = $value['item_id'];
                            $PumpCurrentStocks->quantity        = (int) $current_quantity;
                            $PumpCurrentStocks->created_by      = (int)user_id();
                            $PumpCurrentStocks->updated_by      = (int)user_id();
                            $PumpCurrentStocks->division_id     = $request->division_id;
                            $PumpCurrentStocks->district_id     = $request->district_id;
                            $PumpCurrentStocks->upazilla_id     = $request->upazilla_id;
                            $PumpCurrentStocks->union_id        = $request->union_id;
                            $PumpCurrentStocks->save();
                        }

                    }

                }

            }

            DB::commit();

            save_log([
                'data_id'    => $PumpStockInInfos->id,
                'table_name' => 'pump_stock_in_infos'
            ]);

        } catch (\Exception $ex) {

            DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to save data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data save successfully',
            'data'    => $PumpStockInInfos
        ]);
    }

    /**
     * Pump Stock In Infos update
     */
    public function update(Request $request, $id)
    {
        $validationResult = PumpStockInInfosValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $PumpStockInInfos = PumpStockInInfos::find($id);

        if (!$PumpStockInInfos) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {

            $PumpStockInInfos->sub_group_name     = $request->sub_group_name;
            $PumpStockInInfos->sub_group_name_bn  = $request->sub_group_name_bn;
            $PumpStockInInfos->commodity_group_id = (int) $request->commodity_group_id;
            $PumpStockInInfos->updated_by         = (int)user_id();
            $PumpStockInInfos->save();

            DB::commit();

            save_log([
                'data_id'       => $PumpStockInInfos->id,
                'table_name'    => 'pump_stock_in_infos',
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
            'data'    => $PumpStockInInfos
        ]);
    }

     /**
     * stock in inofos Area 
     */
    public function toggleStatus($id)
    {
        $stockInfo = PumpStockInInfos::find($id);

        if (!$stockInfo) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $stockInfo->status = $stockInfo->status ? 0 : 1;
        $stockInfo->update();

        save_log([
            'data_id' => $stockInfo->id,
            'table_name' => 'pump_stock_in_infos',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $stockInfo
        ]);
    }


}
