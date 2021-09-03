<?php 
namespace App\Http\Validations\FarmerOperator;

use Validator;
use Illuminate\Validation\Rule;

class FarmersRatingsValidations
{
    /**
     * Farmers Ratings validate
     */
    public static function validate ($request , $id = 0)
    { 
        $org_id     = $request->org_id;
        $farmer_id  = $request->farmer_id;

        $validator = Validator::make($request->all(), [
            'feedback'      => 'required',
            'rating'        => 'required',
            'org_id'        => 'required',
            'division_id'   => 'required',
            'district_id'   => 'required',
            'upazilla_id'   => 'required',
            'farmer_id'     => [
                                'required',
                                Rule::unique('far_ratings')->where(function ($query) use($org_id, $id) {
                                    $query->where('org_id', $org_id);
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