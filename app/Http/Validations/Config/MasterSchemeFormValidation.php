<?php
namespace app\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class MasterSchemeFormValidation
{
    /**
     * Master Scheme  form Validation
    */
    public static function validate($request, $id = 0)
  {
    $org_id             = $request->org_id;
    $scheme_type_id     = $request->scheme_type_id;
    
    $validator = Validator::make($request->all(), [
      'org_id'      => 'required',
      'scheme_type_id' => [
          'required',
          Rule::unique('master_scheme_affidavits')->where(function ($query) use($org_id, $scheme_type_id , $id) {
              $subQuery = $query->where('org_id', $org_id)
                           ->where('scheme_type_id', $scheme_type_id);
              if ($id) {
                $subQuery = $subQuery->where('id','!=',$id);
              }

              return $subQuery;
          }),
      ],
        'affidavit'      => 'required',
        'affidavit_bn'   => 'required'
    ]);

    if ($validator->fails()) {
        return [
            'success' => false,
            'errors' => $validator->errors()
        ];
    }
    return ['success' => true];
  }
}
