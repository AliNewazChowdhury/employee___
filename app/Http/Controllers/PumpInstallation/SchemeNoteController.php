<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use app\Http\Validations\PumpInstallation\SchemeNoteValidation;
use App\Models\PumpInstallation\SchemeNote;
use Illuminate\Http\Request;
use DB;

class SchemeNoteController extends Controller
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
     * get all Scheme reject
     */
    public function index(Request $request)
    {  
        $query = DB::table('far_scheme_notes');

        if ($request->scheme_application_id) {
            $query = $query->where('scheme_application_id', $request->scheme_application_id);           
        }

        $list = $query->get();

        return response([
            'success' => true,
            'message' => 'Scheme note list',
            'data' => $list
        ]);
    }

    /**
     * Scheme reject  store
     */
    public function store(Request $request)
    {   
        $validationResult = SchemeNoteValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {
            $scheme_review                          = new SchemeNote();
            $scheme_review->scheme_application_id   = (int)$request->scheme_application_id;
            $scheme_review->note	                = $request->note;
            $scheme_review->note_bn	                = $request->note_bn;
            $scheme_review->created_by              = (int)user_id();
            $scheme_review->updated_by              = (int)user_id();
            $scheme_review->save();

            save_log([
                'data_id'   => $scheme_review->id,
                'table_name'=> 'far_scheme_notes'
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
            'data'    => $scheme_review
        ]);
    }

    /**
     * Scheme reject update
     */
    public function update(Request $request, $id)
    {
        $validationResult = SchemeNoteValidation:: validate($request, $id);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        $scheme_review = SchemeNote::find($id);

        if (!$scheme_review) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        try {
            $scheme_review->scheme_application_id   = (int)$request->scheme_application_id;
            $scheme_review->note	                = $request->note;
            $scheme_review->note_bn	                = $request->note_bn;
            $scheme_review->updated_by              = (int)user_id();
            $scheme_review->save();

            save_log([
                'data_id'       => $scheme_review->id,
                'table_name'    => 'far_scheme_notes',
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
     * Scheme reject destroy
     */
    public function destroy($id)
    {
        $scheme_review = SchemeNote::find($id);

        if (!$scheme_review) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $scheme_review->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_scheme_notes',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
