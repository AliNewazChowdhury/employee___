<?php 
namespace App\Http\Validations\PumpInstallation;

use Illuminate\Support\Facades\Validator;

class DeepTubewellYearlyFinalReportValidations
{
    /**
    * contract agreement validations
    */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [
            'org_id'             => 'required',
            'pump_id'            => 'required',
            'division_id'        => 'required',
            'district_id'        => 'required',
            'upazilla_id'        => 'required',
            'ms'        => 'numeric',
            'fg'        => 'numeric',
            'updc'        => 'numeric',
            'filter_ms'        => 'numeric',
            'filter_fg'        => 'numeric',
            'filter_updc'        => 'numeric',
            'dia'        => 'numeric',
            'amount'        => 'numeric',
            'vertical_power'        => 'numeric',
            'vertical_unit_consumption'        => 'numeric',
            'turbine_power'        => 'numeric',
            'turbine_unit_consumption'        => 'numeric',
            'head'        => 'numeric',
            'discharge'        => 'numeric',
            'command_area	'        => 'numeric',
            'kharif_1_aus'        => 'numeric',
            'kharif_1_others'        => 'numeric',
            'kharif_1_total'        => 'numeric',
            'kharif_2_aman'        => 'numeric',
            'kharif_2_others'        => 'numeric',
            'borou'        => 'numeric',
            'wheat'        => 'numeric',
            'potato'        => 'numeric',
            'corn'        => 'numeric',
            'mustard'        => 'numeric',
            'lentils'        => 'numeric',
            'vegetables'        => 'numeric',
            'robi_total'        => 'numeric',
            'actual'        => 'numeric',
            'barga'        => 'numeric',
            'beneficial_farmer_total'        => 'numeric',
            'start_reading'        => 'numeric',
            'end_reading'        => 'numeric',
            'total_uses_unit'        => 'numeric',
            'hourly_used_unit'        => 'numeric',
            'total_active_hour'        => 'numeric',
            'hourly_irri_charge'        => 'numeric',
            'recoverable_irri_payment'        => 'numeric',
            'collected_irri_payment'        => 'numeric',
            'unpaid_money'        => 'numeric',
            'total_electricity_cost'        => 'numeric',
            'operator_salary'        => 'numeric',
            'maintance_cost'        => 'numeric',
            'other_cost'        => 'numeric',
            'total_cost'        => 'numeric',
            'total_income'        => 'numeric',
            'kharif_1_aus_per_hector_cost'        => 'numeric',
            'kharif_2_aman_per_hector_cost'        => 'numeric',
            'borou_per_hector_cost'        => 'numeric',
            'wheat_per_hector_cost'        => 'numeric',
            'potato_per_hector_cost'        => 'numeric',
            'vegetables_per_hector_cost'        => 'numeric',
            'corn_per_hector_cost'        => 'numeric',
            'mustard_per_hector_cost'        => 'numeric',
            'lentils_per_hector_cost'        => 'numeric',
            'other_scheme_area'        => 'numeric',
            'llp_expense'        => 'numeric',
            'pontoon_expense'        => 'numeric',
            'kharif_1_llp'        => 'numeric',
            'kharif_2_llp'        => 'numeric',
            'robi_llp'        => 'numeric',
            'kharif_1_total_gonku'        => 'numeric',
            'kharif_2_total_gonku'        => 'numeric',
            'kharif_1_llp'        => 'numeric',
            'kharif_1_llp'        => 'numeric',
            'kharif_1_llp'        => 'numeric',
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