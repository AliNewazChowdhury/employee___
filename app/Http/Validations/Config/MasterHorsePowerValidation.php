<?php
namespace app\Http\Validations\Config;

use Validator;
use Illuminate\Validation\Rule;

class MasterHorsePowerValidation
{
    /**
     * Master Equipment Validation
     */
    public static function validate($request, $id = 0)
    {
        $horse_power    = $request->horse_power;
        $pump_type_id   = $request->pump_type_id;
        $validator = Validator::make($request->all(), [
            'horse_power' => [
                'required',
                Rule::unique('master_horse_powers')->where(function ($query) use ($horse_power, $pump_type_id ,$id) {
                    $query->where('horse_power', $horse_power)
                                 ->where('pump_type_id', $pump_type_id);
                    if ($id) {
                        $query = $query->where('id', '!=' ,$id);
                    }
                    return $query;             
                }),
            ],
            'org_id'        => 'required',
            'pump_type_id'  => 'required',
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