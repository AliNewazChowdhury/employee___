<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Http\Validations\PumpInstallation\FarSchLicenseValidation;
use App\Models\PumpInstallation\FarSchemeLicense;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use Illuminate\Http\Request;
use App\Helpers\GlobalFileUploadFunctoin;
use DB;

class FarSchemeLicenseController extends Controller
{
    /**
     * Scheme project  store
     */
    public function store(Request $request)
    {   
        $validationResult = FarSchLicenseValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }

        try {

            $fsl                         = new FarSchemeLicense();
            $fsl->scheme_application_id  = (int)$request->scheme_application_id;
            $fsl->license_no	         = $request->license_no;            
            $fsl->issue_date	         = (new \DateTime($request->issue_date))->format('Y-m-d');
            $fsl->created_by             = (int)user_id();
            $fsl->updated_by             = (int)user_id();
            $file_path 		= 'pump-installation/license';
            $attachment 	=  $request->file('attachment');
            if($attachment !=null && $attachment !=""){
                $attachment_name = GlobalFileUploadFunctoin::file_validation_and_return_file_name($request, $file_path, 'attachment');
            }
			$fsl->attachment=  $attachment_name ? $attachment_name : null;
			if($fsl->save()){
                GlobalFileUploadFunctoin::file_upload($request, $file_path, 'attachment', $attachment_name);
            }   

            save_log([
                'data_id'   => $fsl->id,
                'table_name'=> 'far_scheme_license'
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
            'data'    => $fsl
        ]);
    }

    /**
     * Scheme license show
     */
    public function show($id)
    {
        $license_details = DB::table('far_scheme_license')                          
                        ->join('far_scheme_application', 'far_scheme_license.scheme_application_id', '=','far_scheme_application.id') 
                        ->select('far_scheme_license.*','far_scheme_application.application_id', 
                                'far_scheme_application.name', 'far_scheme_application.name_bn',
                                'far_scheme_license.attachment'        
                        )
                        ->where('far_scheme_application.id', $id)
                        ->first();   

        if (!$license_details) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }        
        
        return response([
            'success'   => true,
            'message'   => 'Farmer scheme license',
            'data'      => $license_details
        ]);
    }

    /**
     * Scheme license verify
     */
    public function verify(Request $request)
    {
        $license = FarSchemeLicense::find($request->id);

        if (!$license) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }   

        try {

            $farmer_sch_app         = FarmerSchemeApplication::find($request->scheme_application_id);
            $farmer_sch_app->status = $request->status;
            $farmer_sch_app->update();

            $license->is_verified   = (int)$request->is_verified;
            $license->updated_by    = (int)user_id();
            $license->update();

            save_log([
                'data_id'       => $license->id,
                'table_name'    => 'far_scheme_license',
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
            'data'    => $license
        ]);
    }
}
