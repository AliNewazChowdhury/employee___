<?php

namespace App\Http\Controllers\FarmerOperator;

use App\Http\Controllers\Controller;
use App\Models\FarmerOperator\FarmerComplain;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use App\Models\PumpInfoManagement\PumpInfo;
use App\Models\PumpInfoManagement\PumpOperator;
use App\Models\PumpMaintenance\Resunk;
use Illuminate\Http\Request;
use DB;

class ResunkController extends Controller
{
    /**
     * Check operator
    */
    public function checkOperator(Request $request)
    {  
        $pumpOperator = PumpOperator::select('pump_id','pump_operator_user_id')
                                ->where('pump_operator_user_id', $request->operator_id)
                                ->first();

        if (!$pumpOperator) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $pumpInfoId = PumpInfo::find($pumpOperator->pump_id)->pump_id;

        $schemeApplication = DB::table('far_scheme_application')                          
                                ->join('far_scheme_app_details','far_scheme_application.id', '=','far_scheme_app_details.scheme_application_id') 
                                ->select('far_scheme_application.*',                            
                                    'far_scheme_app_details.id as details.id',
                                    'far_scheme_app_details.farmer_id as details.farmer_id',
                                    'far_scheme_app_details.email as details.email',
                                    'far_scheme_app_details.scheme_application_id',
                                    'far_scheme_app_details.sch_man_name',
                                    'far_scheme_app_details.sch_man_name_bn',
                                    'far_scheme_app_details.sch_man_father_name',
                                    'far_scheme_app_details.sch_man_father_name_bn',
                                    'far_scheme_app_details.sch_man_mother_name',
                                    'far_scheme_app_details.sch_man_mother_name_bn',
                                    'far_scheme_app_details.sch_man_division_id',
                                    'far_scheme_app_details.sch_man_district_id',
                                    'far_scheme_app_details.sch_man_upazilla_id',
                                    'far_scheme_app_details.sch_man_union_id',
                                    'far_scheme_app_details.sch_man_village',
                                    'far_scheme_app_details.sch_man_village_bn',
                                    'far_scheme_app_details.sch_man_mobile_no',
                                    'far_scheme_app_details.sch_man_nid',
                                    'far_scheme_app_details.pump_division_id',
                                    'far_scheme_app_details.pump_district_id',
                                    'far_scheme_app_details.pump_upazilla_id',
                                    'far_scheme_app_details.pump_union_id',
                                    'far_scheme_app_details.pump_mauza_no',
                                    'far_scheme_app_details.pump_mauza_no_bn',
                                    'far_scheme_app_details.pump_jl_no',
                                    'far_scheme_app_details.pump_jl_no_bn',
                                    'far_scheme_app_details.pump_plot_no',
                                    'far_scheme_app_details.pump_plot_no_bn',
                                    'far_scheme_app_details.scheme_lands',
                                    'far_scheme_app_details.general_minutes',
                                    'far_scheme_app_details.scheme_map',
                                    'far_scheme_app_details.affidavit_id'
                                )
                                ->where('far_scheme_application.status', 11)
                                ->where('far_scheme_application.pump_id', $pumpOperator->pump_id)
                                ->first();
        
        if ($schemeApplication != null) {
            $pumpId = $schemeApplication->pump_id;
        } else {
            $pumpInfo = PumpInfo::select('id')->where('id', $pumpOperator->pump_id)->first();
            $pumpId = $pumpInfo->id;
        }

        $resunk = Resunk::select('complain_id')->where('pump_informations_id', $pumpId)->first();

        if (!$resunk) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $complain = FarmerComplain::select('id')
                                    ->where('id', $resunk->complain_id)
                                    ->where('status', 8) // 8 = mean resunk
                                    ->first();

        if (!$complain) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }
            
        $resunkStatus = DB::table('pump_informations')
                        ->leftjoin('far_resunks', 'pump_informations.id', 'far_resunks.pump_informations_id')
                        ->where('pump_informations.id', $pumpId)
                        ->where('far_resunks.complain_id', $complain->id)
                        ->where('far_resunks.status', 1)
                        ->first();

        $isApplyForResunk = FarmerSchemeApplication::where('application_type_id', 2)
                                                    ->where('farmer_id', $pumpOperator->pump_operator_user_id)
                                                    ->where('status', '!=', 11)
                                                    ->first();

        $resunkStatus = ($resunkStatus != null && $isApplyForResunk == null) ? 1 : 2;

        if (!$resunkStatus) {
            return response([
                'success' => false,
                'message' => 'Data not found.'
            ]);
        }

        $response = [
            'success'       => true,
            'data'          => $schemeApplication,
            'resunkStatus'  => $resunkStatus,
            'pumpInfoId'    => $pumpInfoId,
            'message'       => 'Application details'
        ];

        return response($response);
    }
}
