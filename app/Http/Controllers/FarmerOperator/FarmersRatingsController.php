<?php

namespace App\Http\Controllers\FarmerOperator;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Validations\FarmerOperator\FarmersRatingsValidations;
use App\Models\FarmerOperator\FarmersRatings;
use DB;

class FarmersRatingsController extends Controller
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
     * get all Farmers Ratings
     */
    public function index(Request $request)
    {
        $query = DB::table('far_ratings')
                     ->LeftJoin('far_basic_infos', 'far_ratings.farmer_id', 'far_basic_infos.farmer_id')
                        ->select('far_ratings.*',
                    			'far_basic_infos.name as far_name',
                    			'far_basic_infos.name_bn as far_name_bn'
                    			);

        if ($request->feedback) {
            $query = $query->where('feedback', 'like', "%{$request->feedback}%")
                           ->orWhere('feedback_bn', 'like', "%{$request->feedback}%");
        }

        if ($request->rating) {
            $query = $query->where('rating', $request->rating);
        } 

        if ($request->org_id) {
            $query = $query->where('org_id', $request->org_id);
        }

        if ($request->division_id) {
            $query = $query->where('division_id', $request->division_id);
        }
         
        if ($request->district_id) {
            $query = $query->where('district_id', $request->district_id);
        }
         
        if ($request->upazilla_id) {
            $query = $query->where('upazilla_id', $request->upazilla_id);
        }

        if ($request->farmer_id) {
            $query = $query->where('farmer_id', $request->farmer_id);
        }
        

        $list = $query->paginate($request->per_page??10);


        return response([
            'success' => true,
            'message' => "Farmers ratings list",
            'data' => $list
        ]);
    }

    /**
     * Farmers Ratings store
     */
    public function store(Request $request)
    {
        $validationResult = FarmersRatingsValidations:: validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }
        
        try {
            $farRatings                  = new FarmersRatings();
            $farRatings->feedback        = $request->feedback;   
            $farRatings->feedback_bn     = $request->feedback_bn;    
            $farRatings->rating          = $request->rating;    
            $farRatings->org_id          = (int)$request->org_id;
            $farRatings->division_id     = (int)$request->division_id;
            $farRatings->district_id     = (int)$request->district_id;
            $farRatings->upazilla_id     = (int)$request->upazilla_id;
            $farRatings->farmer_id       = $request->farmer_id;
            $farRatings->created_at      =  date('Y-m-d H:i:s');     
            $farRatings->save();

            save_log([
                'data_id'    => $farRatings->id,
                'table_name' => 'far_ratings'
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
            'data'    => $farRatings
        ]);
    }

    /**
     * Farmers Ratings update
     */
    public function update(Request $request, $id)
    {
        $validationResult = FarmersRatingsValidations:: validate($request,$id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $farRatings = FarmersRatings::find($id);

        if (!$farRatings) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try { 
            $farRatings->feedback        = $request->feedback;   
            $farRatings->feedback_bn     = $request->feedback_bn;    
            $farRatings->rating          = $request->rating;    
            $farRatings->org_id          = (int)$request->org_id;
            $farRatings->division_id     = (int)$request->division_id;
            $farRatings->district_id     = (int)$request->district_id;
            $farRatings->upazilla_id     = (int)$request->upazilla_id;
            $farRatings->farmer_id       = $request->farmer_id;
            $farRatings->updated_at      = date('Y-m-d H:i:s');  
            $farRatings->update();

            save_log([
                'data_id'       => $farRatings->id,
                'table_name'    => 'far_ratings',
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
            'data'    => $farRatings
        ]);
    }

 
    /**
     * Farmers Ratings destroy
     */
    public function destroy($id)
    {
        $farRatings = FarmersRatings::find($id);

        //dd($farRatings);

        if (!$farRatings) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $farRatings->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_ratings',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}

