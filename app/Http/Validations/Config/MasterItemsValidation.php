<?php 
namespace App\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class MasterItemsValidation
{
    /**
     * Master Item Categories validate
     */
    public static function validate ($request , $id = 0)
    { 
        $org_id         = $request->org_id;
        $category_id    = $request->category_id;
        $item_name      = $request->item_name;
        $item_name_bn   = $request->item_name_bn;
        $item_code      = $request->item_code;

        $validator = Validator::make($request->all(), [
            'org_id' => 'required',
            'category_id' => 'required',
            'item_name' => [
                'required',
                Rule::unique('master_items')->where(function ($query) use($org_id, $category_id, $item_name, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('category_id', $category_id);
                    $query->where('item_name', $item_name);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ],

            'item_name_bn' => [
                'required',
                Rule::unique('master_items')->where(function ($query) use($org_id, $item_name_bn, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('item_name_bn', $item_name_bn);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ],

            'item_code' => [
                'required',
                Rule::unique('master_items')->where(function ($query) use($org_id, $category_id, $item_code, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('category_id', $category_id);
                    $query->where('item_code', $item_code);
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