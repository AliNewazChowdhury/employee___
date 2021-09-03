<?php
namespace App\Http\Validations\Config;

use Illuminate\Validation\Rule;
use Validator;

class MasterPumpInstallationProgressTypeValidation  
{
    /**
     * Master Scheme  form Validation
    */
    public static function validate($request ,$id=0)
    {
        $org_id = $request->org_id;
        $pump_type_id = $request->pump_type_id;
        $application_type_id   = $request->application_type_id;
        $validator = Validator::make($request->all(), [
            'pump_type_id' => [
                'required',
                Rule::unique('master_pump_progress_types')->where(function ($query) use($pump_type_id, $application_type_id ,$id) {
                    $query->where('pump_type_id', $pump_type_id)
                                 ->where('application_type_id', $application_type_id);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ],
            'org_id'            => 'required',
            'application_type_id' => 'required',
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