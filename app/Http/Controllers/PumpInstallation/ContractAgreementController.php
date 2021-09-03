<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\ContractAgreementDocumentValidation;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Http\Validations\PumpInstallation\ContractAgreementValidation;
use App\Models\PumpInstallation\ContractAgreement;
use App\Models\PumpInstallation\ContractAgreementDocument;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use DB;

class ContractAgreementController extends Controller
{
    /**
     * Contract agreement store
     */
    public function store(Request $request)
    {   
        $validationResult = ContractAgreementValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {

            $ca                         = new ContractAgreement();
            $ca->scheme_application_id  = (int)$request->scheme_application_id;
            $ca->agreement_details      = $request->agreement_details;            
            $ca->agreement_details_bn   = $request->agreement_details_bn;
            $ca->created_by             = (int)user_id();
            $ca->updated_by             = (int)user_id();
            $ca->save();

            save_log([
                'data_id'   => $ca->id,
                'table_name'=> 'far_scheme_agreement'
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
            'data'    => $ca
        ]);
    }

    /**
     * Contract agreement document store
     */
    public function documentStore(Request $request)
    {   
        $validationResult = ContractAgreementDocumentValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        DB::beginTransaction();

        try {

            $farmer_sch_app         = FarmerSchemeApplication::find($request->scheme_application_id);
            $farmer_sch_app->status = 12; //12 mean application in participation fee stage
            $farmer_sch_app->update();

            save_log([
                'data_id'       => $farmer_sch_app->id,
                'table_name'    => 'far_scheme_application',
                'execution_type'=> 1
            ]);

            $cad                         = new ContractAgreementDocument();
            $cad->scheme_application_id  = (int)$request->scheme_application_id;
            $cad->agreement_date         = (new \DateTime($request->agreement_date))->format('Y-m-d');     
            $cad->created_by             = (int)user_id();
            $cad->updated_by             = (int)user_id();
            $file_path 		= 'pump-installation/agreement-document';
            $attachment 	=  $request->file('attachment');
            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path,'attachment');
            }
			$cad->attachment=  $attachment_name ? $attachment_name : null;

			if($cad->save()){
                GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
            }

            save_log([
                'data_id'   => $cad->id,
                'table_name'=> 'far_scheme_agreemt_doc'
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
            'data'    => $cad
        ]);
    }

    /**
     * contract Agreement pdf
     */
    public function getAgreementPdf(Request $request,$scheme_application_id)
    {
        $query = DB::table('far_scheme_agreement')
                ->join('far_scheme_application','far_scheme_agreement.scheme_application_id', '=','far_scheme_application.id')
                ->select('far_scheme_agreement.agreement_details','far_scheme_agreement.agreement_details_bn',
                        'far_scheme_application.application_id','far_scheme_application.name','far_scheme_application.name_bn')
                        ->where('far_scheme_agreement.scheme_application_id',$scheme_application_id)
                        ->first();
         $list = $query;
         
         return response()->json([
             'success' => true,
             'message' => 'Agreement pdf data list',
              'data' =>$list
         ]);
                        
    }
}
