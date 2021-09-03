<?php 
namespace App\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class MasterItemCategoriesValidations
{
    /**
     * Master Item Categories validate
     */
    public static function validate ($request , $id = 0)
    { 
        $org_id = $request->org_id;
        $category_name = $request->category_name;
        $category_name_bn = $request->category_name_bn;

        $validator = Validator::make($request->all(), [
            'org_id' => 'required',
            'category_name' => [
                'required',
                Rule::unique('master_item_categories')->where(function ($query) use($org_id, $category_name, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('category_name', $category_name);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ],

            'category_name_bn' => [
                'required',
                Rule::unique('master_item_categories')->where(function ($query) use($org_id, $category_name_bn, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('category_name_bn', $category_name_bn);
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