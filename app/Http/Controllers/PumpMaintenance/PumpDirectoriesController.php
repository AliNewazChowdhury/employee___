<?php
namespace App\Http\Controllers\PumpMaintenance;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\PumpMaintenance\PumpDirectoriesValidations;
use App\Models\PumpMaintenance\PumpDirectories;
use App\Models\PumpMaintenance\PumpDirectoryEquipments;
use App\Helpers\GlobalFileUploadFunctoin;
use DB;

class PumpDirectoriesController extends Controller
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
    * get all Pump Directories
    */
    public function index(Request $request)
    {
        $query  = PumpDirectories::with('pumpDirectoryEquipments');

        if ($request->type_id) {
            $query = $query->where('type_id', $request->type_id);
        }

        if ($request->name) {
            $query = $query->where('name', 'like', "%{$request->name}%")
                        ->orWhere('name_bn', 'like', "%{$request->name}%");
        }

        if ($request->village_name) {
            $query = $query->where('village_name', 'like', "%{$request->village_name}%")
                        ->orWhere('village_name_bn', 'like', "%{$request->village_name}%");
        }

        if ($request->division_id) {
            $query = $query->where('division_id', $request->division_id);
        }

         if ($request->district_id) {
            $query = $query->where('district_id', $request->district_id);
        }

        if ($request->upazila_id) {
            $query = $query->where('upazila_id', $request->upazila_id);
        }

        if ($request->union_id) {
            $query = $query->where('union_id', $request->union_id);
        }

        if ($request->from_date) {
            $query = $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->to_date) {
            $query = $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->to_date)));
        }

        $list = $query->paginate($request->per_page??10);

        return response([
            'success' => true,
            'message' => 'Pump Directories list',
            'data' => $list
        ]);
    }

    /**
    * get Loged in Pump Directories
    */
    public function singleIndex(Request $request)
    {
        $userId = user_id() ?? 1;
        $query  = PumpDirectories::with('pumpDirectoryEquipments')
                                ->where('status', 0)
                                ->where('created_by', $userId)
                                ->get();
        return response([
            'success' => true,
            'message' => 'Pump Directories',
            'data' => $query
        ]);
    }

    /**
    * get all Pump Directories
    */
    public function indexReport(Request $request)
    {
        $query = PumpDirectories::with('pumpDirectoryEquipments');

        if ($request->type_id) {
            $query = $query->where('type_id', $request->type_id);
        }

        if ($request->name) {
            $query = $query->where('name', 'like', "%{$request->name}%")
                        ->orWhere('name_bn', 'like', "%{$request->name}%");
        }

        if ($request->village_name) {
            $query = $query->where('village_name', 'like', "%{$request->village_name}%")
                        ->orWhere('village_name_bn', 'like', "%{$request->village_name}%");
        }

        if ($request->division_id) {
            $query = $query->where('division_id', $request->division_id);
        }

         if ($request->district_id) {
            $query = $query->where('district_id', $request->district_id);
        }

        if ($request->upazila_id) {
            $query = $query->where('upazila_id', $request->upazila_id);
        }

        if ($request->union_id) {
            $query = $query->where('union_id', $request->union_id);
        }

        if ($request->from_date) {
            $query = $query->whereDate('created_at', '<=', date('Y-m-d', strtotime($request->from_date)));
        }

        if ($request->to_date) {
            $query = $query->whereDate('created_at', '>=', date('Y-m-d', strtotime($request->to_date)));
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Pump Directories list',
            'data' => $list
        ]);
    }

    /**
    * Pump Directories store
    */
    public function store(Request $request)
    {
        // return $request;
        $validationResult = PumpDirectoriesValidations:: validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path      = 'pump-directories';
        $attachment     =  $request->file('attachment');

        DB::beginTransaction();

        try {
            $pumpDirectories                  = new PumpDirectories();
            $pumpDirectories->type_id          = $request->type_id;
            $pumpDirectories->name             = json_decode($request->name);
            $pumpDirectories->name_bn          = json_decode($request->name_bn);
            $pumpDirectories->village_name     = json_decode($request->village_name);
            $pumpDirectories->village_name_bn  = json_decode($request->village_name_bn);
            $pumpDirectories->address          = json_decode($request->address);
            $pumpDirectories->address_bn       = json_decode($request->address_bn);
            $pumpDirectories->latitude         = json_decode($request->latitude);
            $pumpDirectories->longitude        = json_decode($request->longitude);
            $pumpDirectories->mobile           = json_decode($request->mobile);
            $pumpDirectories->email            = json_decode($request->email);
            $pumpDirectories->document_name    = json_decode($request->document_name);
            $pumpDirectories->document_name_bn = json_decode($request->document_name_bn);
            $pumpDirectories->division_id      = $request->division_id;
            $pumpDirectories->district_id      = $request->district_id;
            $pumpDirectories->upazila_id       = $request->upazila_id;
            $pumpDirectories->union_id         = $request->union_id;
            $pumpDirectories->created_by       = (int)user_id();

            $attachment_name ="";
            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
            }
            $pumpDirectories->attachment       = $attachment_name ?? null;

            if($pumpDirectories->save()){

                $equipments = json_decode($request->pump_directory_equipments);

                foreach ($equipments as  $equipment) {
                    $pumpDirectoryEquipments                             = new PumpDirectoryEquipments();
                    $pumpDirectoryEquipments->pump_directory_id          = $pumpDirectories->id;
                    $pumpDirectoryEquipments->master_equipment_type_id   = $equipment->master_equipment_type_id;
                    $pumpDirectoryEquipments->details                    = $equipment->details;
                    $pumpDirectoryEquipments->details_bn                 = $equipment->details_bn;
                    $pumpDirectoryEquipments->save();
                }

                GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
            }

            DB::commit();

            save_log([
                'data_id'    => $pumpDirectories->id,
                'table_name' => 'master_item_sub_categories'
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
            'data'    => $pumpDirectories
        ]);
    }

    /**
     * Pump Directories update
    */
    public function update(Request $request, $id)
    {   //return $request;
        $validationResult = PumpDirectoriesValidations:: validate($request ,$id);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $file_path          = 'pump-directories';
        $attachment         = $request->file('attachment');
        $pumpDirectories    = PumpDirectories::find($id);
        $old_file           = $pumpDirectories->attachment;

        if (!$pumpDirectories) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();

        try {
            $pumpDirectories->type_id          = $request->type_id;
            $pumpDirectories->name             = json_decode($request->name);
            $pumpDirectories->name_bn          = json_decode($request->name_bn);
            $pumpDirectories->village_name     = json_decode($request->village_name);
            $pumpDirectories->village_name_bn  = json_decode($request->village_name_bn);
            $pumpDirectories->address          = json_decode($request->address);
            $pumpDirectories->address_bn       = json_decode($request->address_bn);
            $pumpDirectories->latitude         = json_decode($request->latitude);
            $pumpDirectories->longitude        = json_decode($request->longitude);
            $pumpDirectories->mobile           = json_decode($request->mobile);
            $pumpDirectories->email            = json_decode($request->email);
            $pumpDirectories->document_name    = json_decode($request->document_name);
            $pumpDirectories->document_name_bn = json_decode($request->document_name_bn);
            $pumpDirectories->division_id      = $request->division_id;
            $pumpDirectories->district_id      = $request->district_id;
            $pumpDirectories->upazila_id       = $request->upazila_id;
            $pumpDirectories->union_id         = $request->union_id;
            $pumpDirectories->updated_by       = (int)user_id();

            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
            }

            $pumpDirectories->attachment       = $attachment_name ?? $old_file;

            if($pumpDirectories->save()){

                $previousDirEquipments = PumpDirectoryEquipments::where('pump_directory_id', $id)->delete();

                $equipments = json_decode($request->pump_directory_equipments);

                foreach ($equipments as $equipment) {
                    $pumpDirectoryEquipments                             = new PumpDirectoryEquipments();
                    $pumpDirectoryEquipments->pump_directory_id          = $pumpDirectories->id;
                    $pumpDirectoryEquipments->master_equipment_type_id   = $equipment->master_equipment_type_id;
                    $pumpDirectoryEquipments->details                    = $equipment->details;
                    $pumpDirectoryEquipments->details_bn                 = $equipment->details_bn;
                    $pumpDirectoryEquipments->save();
                }

                if($attachment !=null && $attachment !=""){
                    GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name, $old_file);
                }
            }

            DB::commit();

            save_log([
                'data_id'       => $pumpDirectories->id,
                'table_name'    => 'master_item_sub_categories',
                'execution_type'=> 1
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
            'message' => 'Data update successfully',
            'data'    => $pumpDirectories
        ]);
    }

    /**
     * PumpDirectories status update
     */
    public function toggleStatus($id)
    {
        $pumpDirectories = PumpDirectories::find($id);

        if (!$pumpDirectories) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }
        $pumpDirectories->status = $pumpDirectories->status ? 0 : 1;
        $pumpDirectories->update();

        save_log([
            'data_id'       => $pumpDirectories->id,
            'table_name'    => 'pump_directories',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $pumpDirectories
        ]);
    }

    /**
     * Pump Directories destroy
     */
    public function destroy($id)
    {
        $pumpDirectories = PumpDirectories::find($id);

        if (!$pumpDirectories) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        DB::beginTransaction();
        try {
            PumpDirectoryEquipments::where('pump_directory_id', $id)->delete();
            $pumpDirectories->delete();

            DB::commit();
            save_log([
                'data_id'       => $id,
                'table_name'    => 'pump_directories',
                'execution_type'=> 2
            ]);
        } catch (\Exception $ex) {
            DB::rollback();

            return response([
                'success' => false,
                'message' => 'Failed to delete data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : []
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }

}
