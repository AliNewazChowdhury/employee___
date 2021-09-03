<?php 
namespace App\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class MasterItemSubCategoriesValidations
{
    /**
     * Master Item Sub-Categories validate
     */
    public static function validate ($request , $id = 0)
    { 
        $org_id                 = $request->org_id;
        $category_id            = $request->category_id;
        $sub_category_name      = $request->sub_category_name;
        $sub_category_name_bn   = $request->sub_category_name_bn;



        $validator = Validator::make($request->all(), [
            'org_id' => 'required',
            'sub_category_name' => [
                'required',
                Rule::unique('master_item_sub_categories')->where(function ($query) use($org_id,$category_id, $sub_category_name, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('category_id', $category_id);
                    $query->where('sub_category_name', $sub_category_name);
                    if ($id) {
                        $query =$query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ],

            'sub_category_name_bn' => [
                'required',
                Rule::unique('master_item_sub_categories')->where(function ($query) use($org_id, $category_id, $sub_category_name_bn, $id) {
                    $query->where('org_id', $org_id);
                    $query->where('category_id', $category_id);
                    $query->where('sub_category_name_bn', $sub_category_name_bn);
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