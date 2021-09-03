<?php
namespace app\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class MasterProjectValidation
{
    /**
     *Master Project Validation
     */

    public static function validate($request, $id = 0)
    {
        $org_id     = $request->org_id;
        $validator = Validator::make($request->all(), [
            'org_id'            => 'required',
            'project_name_bn'   => 'required',
            'project_name'      => ['required',
                                Rule::unique('master_projects')->where(function ($query) use($org_id, $id) {
                                    $query->where('org_id', $org_id);
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
