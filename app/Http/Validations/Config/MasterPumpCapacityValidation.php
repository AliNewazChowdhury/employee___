<?php
namespace app\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class MasterPumpCapacityValidation
{
    /**
     *Master Scheme Validation 
    */
    public static function validate($request, $id = 0)
    {
        $org_id     = $request->org_id;
        $master_scheme_type_id     = $request->master_scheme_type_id;
        $validator = Validator::make($request->all(), [
            'org_id'    => 'required',
            'capacity'  => ['required',
                                Rule::unique('master_pump_capacities')->where(function ($query) use($org_id, $master_scheme_type_id, $id) {
                                    $query->where('org_id', $org_id)
                                          ->where('master_scheme_type_id', $master_scheme_type_id);
                                    if ($id) {
                                        $query =$query->where('id', '!=' ,$id);
                                    }
                                    return $query;
                                }),
                            ]
        ]);

        if ($validator->fails()) {
            return([
                'success' => false,
                'errors'  => $validator->errors()
            ]);
        }
        
        return ['success'=>true];
    }
}