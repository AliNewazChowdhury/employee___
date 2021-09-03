<?php
namespace app\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class SubSchemeTypeValidation
{
    /**
     *Master Scheme Validation 
    */
    public static function validate($request, $id = 0)
    {
        $org_id     = $request->org_id;
        $master_scheme_type_id     = $request->master_scheme_type_id;
        $validator = Validator::make($request->all(), [
            'org_id'                    => 'required',
            'sub_scheme_type_name_bn'   => 'required',
            'sub_scheme_type_name'      => ['required',
                                Rule::unique('sub_scheme_types')->where(function ($query) use($org_id, $master_scheme_type_id, $id) {
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