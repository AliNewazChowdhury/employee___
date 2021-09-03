<?php

namespace App\Http\Controllers\PumpMaintenance;

use App\Http\Controllers\Controller;
use App\Models\FarmerOperator\FarmerComplain;
use App\Models\PumpMaintenance\ComplainReview;
use App\Models\PumpMaintenance\ComplainResolved;
use Illuminate\Http\Request;
use DB;

class ComplainController extends Controller
{
    /**
     * show all complain of pump
     */
    public function index (Request $request) 
    {   
        $query = DB::table('far_complains')
                    ->Join('far_basic_infos','far_complains.farmer_id', '=','far_basic_infos.farmer_id')
                    ->select('far_complains.*','far_basic_infos.name_bn', 'far_basic_infos.name')
                    ->where('far_complains.status',1)
                    ->orderBy('far_complains.id','DESC');
        
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
            'message' => 'Complain list data',
            'data' => $list
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
     * Farmer complain resolved
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
     * Farmer complain review
     */
    public function review(Request $request)
    {   
        DB::beginTransaction();

        try {

            $compReview                   = new ComplainReview();
            $compReview->complain_id      = (int)$request->complain_id;
            $compReview->review_note      = $request->review_note;
            $compReview->review_note_bn   = $request->review_note_bn;
            $compReview->created_by       = (int)user_id();
            $compReview->updated_by       = (int)user_id();
            $compReview->save();

            $farComplain = FarmerComplain::find($request->complain_id);
            $farComplain->status = 3;
            $farComplain->update();

            DB::commit();
            
            save_log([
                'data_id'       => $compReview->id,
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
     * Farmer complain send to required maintenance
     */
    public function send(Request $request)
    {   
        try {

            $farComplain = FarmerComplain::find($request->complain_id);
            $farComplain->status = 4;
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
    public function dashboard(Request $request)
    {
        $query =FarmerComplain::latest();
        if (!empty($request->org_id)) {
            $query = $query->where('org_id', $request->org_id);
        }
        $total=$query->get();
        return response([
            'success' => true,
            'message' => 'FarmerComplain list',
            'data' => array(
                'total' =>$total->count(),
                'pending' =>$total->where('status',1)->count(),
                'complete' =>$total->where('status',7)->count()
            )
        ]);
    }
}
