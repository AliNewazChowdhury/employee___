<?php

namespace App\Http\Controllers\Config;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\Config\MasterCircleAreaValidation;
use App\Models\Config\MasterCircleArea;

class MasterCircleAreaController extends Controller
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
     * get all  Master Circle  infotmation
     */
    public function getlist(Request $request)
    {
        $query = MasterCircleArea::get();

        return response([
            'success' => true,
            'message' => 'All Circle List',
            'data' => $query
        ]);
    }
    /**
     * get all  Master Circle Area infotmation
     */
    public function index(Request $request)
    {
        $query=MasterCircleArea::select('*');

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        } 

        if ($request->division_id) {
            $query = $query->where('division_id', $request->division_id);
        }

        if ($request->district_id) {
            $query = $query->where('district_id', $request->district_id);
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }
     
        $list = $query->paginate($request->per_page ?? env('PER_PAGE') );

        return response([
            'success' => true,
            'message' => 'Master Circle  list',
            'data' => $list
        ]);
    }

    /**
     * Master Circle Area  store 
     */
    public function store(Request $request)
    {  
        $validationResult = MasterCircleAreaValidation:: validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {
            $circleArea                       = new MasterCircleArea();
            $circleArea->org_id               = (int)$request->org_id;
            $circleArea->division_id          = (int)$request->division_id;
            $circleArea->district_id          = (int)$request->district_id;
            $circleArea->circle_area_name     = $request->circle_area_name;
            $circleArea->circle_area_name_bn  = $request->circle_area_name_bn;
            $circleArea->created_by           = (int)user_id();
            $circleArea->updated_by           = (int)user_id();
            $circleArea->save();

            save_log([
                'data_id' => $circleArea->id,
                'table_name' => 'master_circle_areas',
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
            'data'    => $circleArea
        ]);
    }

    /**
     * Master Circle Area Update
     */
    public function update(Request $request, $id)
    {
        $validationResult = MasterCircleAreaValidation:: validate($request ,$id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $circleArea = MasterCircleArea::find($id);

        if (!$circleArea) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $circleArea->org_id               = (int)$request->org_id;
            $circleArea->division_id          = (int)$request->division_id;
            $circleArea->district_id          = (int)$request->district_id;
            $circleArea->circle_area_name     = $request->circle_area_name;
            $circleArea->circle_area_name_bn  = $request->circle_area_name_bn;
            $circleArea->updated_by           = (int)user_id();
            $circleArea->update();

            save_log([
                'data_id' => $circleArea->id,
                'table_name' => 'master_circle_areas',
                'execution_type' => 1
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
            'data'    => $circleArea
        ]);
    }

    /**
     * Master Circle Area 
     */
    public function toggleStatus($id)
    {
        $circleArea = MasterCircleArea::find($id);

        if (!$circleArea) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $circleArea->status = $circleArea->status ? 0 : 1;
        $circleArea->update();

        save_log([
            'data_id' => $circleArea->id,
            'table_name' => 'master_circle_areas',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data updated successfully',
            'data'    => $circleArea
        ]);
    }

    /**
     * Master Circle Area Destroy
     */
    public function destroy($id)
    {
        $circleArea = MasterCircleArea::find($id);

        if (!$circleArea) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $circleArea->delete();

        save_log([
            'data_id' => $circleArea->id,
            'table_name' => 'master_circle_areas',
            'execution_type' => 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
