<?php

namespace App\Http\Controllers\PumpOperator;

use App\Helpers\GlobalFileUploadFunctoin;
use App\Http\Controllers\Controller;
use App\Models\PumpOperator\FarmerPumpOperatorDocuments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FarmerPumpOperatorDocumentController extends Controller
{
    /**
     * get application documents
     */
    public function index($application_id)
    {
        $query = FarmerPumpOperatorDocuments::where('pump_opt_apps_id', $application_id)->get();

        if ($query->count() > 0) {
            return response([
                'success' => true,
                'message' => 'Application document list',
                'data' => $query
            ]);
        } else {
            return response([
                'success' => false,
                'message' => 'Data not found',
                'data'    => []
            ]);
        }

    }

    /**
     * application document store
     */
    public function store(Request $request)
    {       
        $file_path 	= 'pump-operator-document';

        try {
            $attachment_name = null;

            $document = new FarmerPumpOperatorDocuments();
            $document->user_id              = $request->user_id;
            $document->document_title       = $request->document_title;
            $document->document_title_bn    = $request->document_title_bn;

            if($request->attachment && $request->attachment != ""){

                $file = $request->attachment;
                $rules = ['attachment' => 'required|mimes:png,gif,jpeg,svg,tiff,pdf,doc,docx,tex,txt,rtf'];
                $validator = Validator::make(['attachment' => $file], $rules);

                $attachment_name = time().'.' . $file->getClientOriginalExtension();

                if ($validator->fails()) {
                    return ([
                        'success' => false,
                        'errors' => $validator->errors()
                    ]);
                }
            }

            $document->attachment   =  $attachment_name ?? null;

            if($document->save()){
                $fileDestinationPath = storage_path("uploads/{$file_path}/original");

                if ($file != null && $file != "") {
                    GlobalFileUploadFunctoin::is_dir_set_permission($fileDestinationPath);
                    $file->move($fileDestinationPath, $attachment_name);
                }
            }

            save_log([
                'data_id'   => $document->id,
                'table_name'=> 'far_pump_opt_docs'
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
            'data'    => $document
        ]);
    }

    /**
     * application document delete
     */
    public function destroy($id)
    {
        $document = FarmerPumpOperatorDocuments::find($id);

        if (!$document) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $file = $document->attachment;
        $fileDestinationPath = storage_path('uploads/pump-operator-document/original');

        if(file_exists($fileDestinationPath.'/'. $file)){
            unlink($fileDestinationPath.'/'.$file);
        }

        $document->delete();

        save_log([
            'data_id'       => $id,
            'table_name'    => 'far_pump_opt_docs',
            'execution_type'=> 2
        ]);

        return response([
            'success' => true,
            'message' => 'Data deleted successfully'
        ]);
    }
}
