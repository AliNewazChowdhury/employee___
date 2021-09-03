<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\SchemeReviewValidation;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInstallation\SchemeReview;
use Illuminate\Http\Request;
use DB;

class SchemeReviewController extends Controller
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
     * get all Scheme Review
     */
    public function index(Request $request)
    {
        $query = DB::table('far_scheme_reviews')
                ->join('far_scheme_application','far_scheme_reviews.scheme_application_id', '=','far_scheme_application.id')                
                ->select('far_scheme_reviews.*',
                        'far_scheme_application.name as farmer_name','far_scheme_application.name_bn as farmer_name_bn'
             );

        if ($request->scheme_application_id) {
            $query = $query->where('scheme_application_id', $request->scheme_application_id);           
        }

        if ($request->status) {
            $query = $query->where('status', $request->status);
        }

        

        $list = $query->paginate($request->per_page);

        return response([
            'success' => true,
            'message' => 'Scheme review list',
            'data' => $list
        ]);
    }

    /**
     * Scheme Review  store
     */
    public function store(Request $request)
    {   
        $validationResult = SchemeReviewValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();
        
        try {

            $farmer_sch_app         = FarmerSchemeApplication::find($request->scheme_application_id);
            $farmer_sch_app->status = $request->status;
            $farmer_sch_app->update();

            save_log([
                'data_id'       => $farmer_sch_app->id,
                'table_name'    => 'far_scheme_application',
                'execution_type'=> 1
            ]);

            $scheme_review                          = new SchemeReview();
            $scheme_review->scheme_application_id   = (int)$request->scheme_application_id;
            $scheme_review->review_note	            = $request->review_note;
            $scheme_review->review_note_bn	        = $request->review_note_bn;
            $scheme_review->created_by              = (int)user_id();
            $scheme_review->updated_by              = (int)user_id();
            $scheme_review->save();

            save_log([
                'data_id'   => $scheme_review->id,
                'table_name'=> 'far_scheme_reviews'
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
            'message' => 'Data save successfully',
            'data'    => $farmer_sch_app
        ]);
    }

    /**
     * Scheme Review update
     */
    public function update(Request $request, $id)
    {
        $validationResult = SchemeReviewValidation:: validate($request, $id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $scheme_review = SchemeReview::find($id);

        if (!$scheme_review) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $scheme_review->scheme_application_id  = (int)$request->scheme_application_id;
            $scheme_review->review_note	        = $request->review_note;
            $scheme_review->review_note_bn	        = $request->review_note_bn;
            $scheme_review->updated_by             = (int)user_id();
            $scheme_review->save();

            save_log([
                'data_id'       => $scheme_review->id,
                'table_name'    => 'far_scheme_reviews',
                'execution_type'=> 1
            ]);

        } catch (\Exception $ex) {
            return response([
                'success' => false,
                'message' => 'Failed to update data.',
                'errors'  => env('APP_ENV') !== 'production' ? $ex->getMessage() : ""
            ]);
        }

        return response([
            'success' => true,
            'message' => 'Data update successfully',
            'data'    => $scheme_review
        ]);
    }

    /**
     * Scheme Review destroy
     */
    public function destroy($id)
    {
        $scheme_review = SchemeReview::find($id);

        if (!$scheme_review) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $scheme_review->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_scheme_reviews',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
