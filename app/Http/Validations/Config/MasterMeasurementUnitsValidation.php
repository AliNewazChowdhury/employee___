<?php 
namespace App\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class MasterMeasurementUnitsValidation
{
    /**
     * Master Item Categories validate
     */
    public static function validate ($request , $id = 0)
    { 
        $org_id         = $request->org_id;
        $unit_name      = $request->unit_name;
        $unit_name_bn   = $request->unit_name_bn;

        $validator = Validator::make($request->all(), [
            'org_id' => 'required',
            'unit_name' => [
                'required',
                Rule::unique('master_measurement_units')->where(function ($query) use($org_id, $unit_name, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('unit_name', $unit_name);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ],

            'unit_name_bn' => [
                'required',
                Rule::unique('master_measurement_units')->where(function ($query) use($org_id, $unit_name_bn, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('unit_name_bn', $unit_name_bn);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ]            

        ]);




        if ($validator->fails()) {
            return ([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        return ['success'=> 'true'];
    }
}