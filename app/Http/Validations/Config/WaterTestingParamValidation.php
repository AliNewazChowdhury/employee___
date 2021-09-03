<?php
namespace app\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class WaterTestingParamValidation
{
    /**
     *Master Water Testing Parameter
    */
    public static function validate($request, $id = 0)
    {
        $validator = Validator::make($request->all(), [
            'org_id'    => 'required',
            'testing_type_id'   => 'required',
            'name' => [
                'required',
                Rule::unique('water_testing_parameters')->where(function ($query) use($request, $id) {
                    $query->where('testing_type_id', $request->testing_type_id);
                    $query->where('org_id', $request->org_id);
                    $query->where('name', $request->name);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ],
            'name_bn' => [
                'required',
                Rule::unique('water_testing_parameters')->where(function ($query) use($request, $id) {
                    $query->where('testing_type_id', $request->testing_type_id);
                    $query->where('org_id', $request->org_id);
                    $query->where('name_bn', $request->name_bn);
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