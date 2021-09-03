<?php
namespace app\Http\Validations\FarmerProfile;

use Illuminate\Validation\Rule;
use Validator;

class  FarmerBasicInfosValidations
{
    /**
     * Farmer Basic Infos Validation
     */
    public static function validate($request ,$id = 0)
    {
        $nid   = $request->nid;
        $validator = Validator::make($request->all(), [
            'name'       		=> 'required',
			'name_bn'       	=> 'required',
            'nid' => [
                'required',
                Rule::unique('far_basic_infos')->where(function ($query) use( $nid ,$id) {
                    $query->where('id', $nid);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$nid);
                    }
                    return $query;
                }),
            ],

			'far_division_id'   => 'required',
			'far_district_id'   => 'required',
			'far_upazilla_id'   => 'required',
			'far_union_id'      => 'required',
			'far_village'       => 'required',
			'far_village_bn'    => 'required'
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
