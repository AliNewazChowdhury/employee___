<?php

namespace app\Http\Validations\PumpMaintenance;

use Validator;

class TroubleEquipmentValidation
{
    /**
     * Pump trouble equipment validation
     */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'complain_id'   => 'required',
            'division_id'   => 'required',
            'district_id'   => 'required',
            'upazilla_id'   => 'required',
            'union_id'      => 'required',
            'mauza_no'      => 'required',
            'jl_no'         => 'required',
            'plot_no'       => 'required',
            'details'       => 'required'
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