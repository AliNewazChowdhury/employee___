<?php

namespace App\Http\Controllers\FarmerOperator;

use App\Http\Controllers\Controller;
use App\Http\Validations\FarmerOperator\FarmerComplainValidation;
use App\Models\FarmerOperator\FarmerComplain;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use DB;

class FarmerComplainController extends Controller
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
     * get all complain of a single farmer
     */
    public function index(Request $request, $farmer_id)
    {
        $query = DB::table('far_complains')
                    ->leftjoin('far_complain_reviews','far_complains.id','far_complain_reviews.complain_id')
                    ->leftjoin('far_complain_resolves','far_complains.id','far_complain_resolves.complain_id')
                    ->leftjoin('far_complain_progress_reports','far_complains.id','far_complain_progress_reports.complain_id')
                    ->select('far_complains.*','far_complain_reviews.id as review_id','far_complain_resolves.id as resolved_id','far_complain_progress_reports.progress_type')
                    ->where('farmer_id', $farmer_id);

        if ($request->complain_id) {
            $query = $query->where('complain_id', $request->complain_id);
        }

        if ($request->start_date) {
            $query = $query->whereDate('created_at', '<=', $request->start_date);
        }

        if ($request->end_date) {
            $query = $query->whereDate('created_at', '>=', $request->end_date);
        }

        $list = $query->paginate(request('per_page', config('app.per_page')));

        return response([
            'success' => true,
            'message' => 'Complain list data',
            'data' => $list
        ]);
    }

    /**
     * Farmer complain store
     */
    public function store(Request $request)
    {
        $validationResult = FarmerComplainValidation::validate($request);

        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $complain = DB::table('far_complains')
                                ->latest()
                                ->select('complain_id')
                                ->orderBy('id','desc')
                                ->first();

        if($complain){
            $complain_id = $complain->complain_id;
            if( $complain_id !="" ){
                $complain_id += 1;
            }
        } else {
            $complain_id = 100000;
        }

        $file_path 		= 'farmer-complain';
        $attachment 	=  $request->file('attachment');

        try {

            $fc                 = new FarmerComplain();
            $fc->farmer_id      = (int)$request->farmer_id;
            $fc->email          = $request->email;
            $fc->complain_id    = $complain_id;
            $fc->org_id         = (int)$request->org_id;
            $fc->far_division_id= (int)$request->far_division_id;
            $fc->far_district_id= (int)$request->far_district_id;
            $fc->far_upazilla_id= (int)$request->far_upazilla_id;
            $fc->far_union_id   = (int)$request->far_union_id;
            $fc->office_id      = (int)$request->office_id;
            $fc->subject        = $request->subject;
            $fc->subject_bn     = $request->subject_bn;
            $fc->details        = $request->details;
            $fc->details_bn     = $request->details_bn;
            $fc->pump_id        = $request->pump_id;

            if($attachment != null && $attachment != ""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
                
            }
            $fc->attachment     =  isset($attachment_name) ? $attachment_name : null;
			if($fc->save()){
                if($attachment != null && $attachment != ""){
                    GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
                }
			}

            save_log([
                'data_id'   => $fc->id,
                'table_name'=> 'far_complains'
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
            'data'    => $fc
        ]);
    }

    /**
     * Farmer complain details
     */
    public function details($id)
    {

        $complain = FarmerComplain::find($id);

        if (!$complain) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        return response([
            'success' => true,
            'data'    => $complain
        ]);
    }

    /**
     * Farmer complain resolved note
     */
    public function resolvedNote($complain_id)
    {  
        $note = FarmerComplain::join('far_complain_resolves','far_complain_resolves.complain_id','far_complains.id')
                                ->select('far_complain_resolves.resolve_note','far_complain_resolves.resolve_note_bn')
                                ->where('far_complain_resolves.complain_id', $complain_id)
                                ->first();

        if (!$note) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        return response([
            'success' => true,
            'data'    => $note
        ]);
    }

    /**
     * Farmer complain review note
     */
    public function reviewNote($complain_id)
    {
        $note = FarmerComplain::join('far_complain_reviews','far_complain_reviews.complain_id','far_complains.id')
                                    ->select('far_complain_reviews.review_note','far_complain_reviews.review_note_bn')
                                    ->where('far_complain_reviews.complain_id', $complain_id)
                                    ->first();

        if (!$note) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        return response([
            'success' => true,
            'data'    => $note
        ]);
    }
}
