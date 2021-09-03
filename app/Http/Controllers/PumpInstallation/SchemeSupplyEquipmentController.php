<?php

namespace App\Http\Controllers\PumpInstallation;

use App\Http\Controllers\Controller;
use App\Models\PumpInstallation\SchemeSupplyEquipment;
use App\Http\Validations\PumpInstallation\SchemeSupplyEquipmentValidation;
use App\Models\FarmerOperator\FarmerSchemeApplication;
use Illuminate\Http\Request;

class SchemeSupplyEquipmentController extends Controller
{
    /**
     * get farmer scheme supply equipment store
     */
    public function store(Request $request)
    {
        $validationResult = SchemeSupplyEquipmentValidation::validate($request);    
        
        if (!$validationResult['success']) {
            return response($validationResult);
        }
        
        try {

            $farmer_sch_app         = FarmerSchemeApplication::find($request->scheme_application_id);
            $farmer_sch_app->status = $request->status;
            $farmer_sch_app->update();

            save_log([
                'data_id'       => $farmer_sch_app->id,
                'table_name'    => 'far_scheme_application',
                'execution_type'=> 1
            ]);
            
            $sse                    = new SchemeSupplyEquipment();
            $sse->supply_note       = $request->supply_note;
            $sse->supply_note_bn    = $request->supply_note_bn;
            $sse->supply_date	    = (new \DateTime($request->supply_date))->format('Y-m-d');
            $sse->requisition_id    = (int)$request->requisition_id;
            $sse->created_by        = (int)user_id();
            $sse->updated_by        = (int)user_id();
            $sse->save();

            save_log([
                'data_id'   => $sse->id,
                'table_name'=> 'far_scheme_supply_equipments'
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
            'data'    => $sse
        ]);
    }

    /**
     * get farmer scheme supply equipment details
     */
    public function details($requisition_id)
    { 
        $supply_equipment = SchemeSupplyEquipment::where('requisition_id', $requisition_id)->first(); 

        if(!$supply_equipment) {
            return response([
                'success' => false,
                'message' => 'Data not found'
            ]);
        } 

        return response([
            'success'   => true,
            'data'      => $supply_equipment
        ]);
    }
}
