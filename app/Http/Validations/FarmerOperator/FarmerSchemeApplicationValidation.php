<?php
namespace app\Http\Validations\FarmerOperator;

use Validator;

class FarmerSchemeApplicationValidation
{
    /**
     * Farmer Scheme Application Validation
     */
    public static function validate($request)
    {
        $validator = Validator::make($request->all(), [	
			'scheme_type_id'            => 'required',	
			'org_id'    	            => 'required',	
            'name'                      => 'required',  
			'name_bn'   	 	        => 'required',	
            'nid'                       => 'required', 
            'far_division_id'           => 'required',
			'far_district_id'           => 'required',	
			'far_upazilla_id'           => 'required',	
			'far_union_id'    	        => 'required',	
			'far_village'    	        => 'required',	
			'far_village_bn'           => 'required',	
			'far_mobile_no'    	        => 'required',              
            'sch_man_name'              => 'required',  
            'sch_man_name_bn'           => 'required',
            'sch_man_division_id'       => 'required',
            'sch_man_district_id'       => 'required',  
            'sch_man_upazilla_id'       => 'required',  
            'sch_man_union_id'          => 'required',  
            'sch_man_village'           => 'required',  
            'sch_man_village_bn'        => 'required',  
            'sch_man_mobile_no'         => 'required',  
            'pump_district_id'          => 'required',  
            'pump_upazilla_id'          => 'required',  
            'pump_union_id'             => 'required',  
            'pump_mauza_no'             => 'required',  
            'pump_mauza_no_bn'          => 'required',  
            'pump_jl_no'                => 'required',  
            'pump_jl_no_bn'             => 'required',  
            'pump_plot_no'              => 'required',  
            'pump_plot_no_bn'           => 'required',
            'command_area_hector'       => 'required'   
            /*'payment_status'    => 'required',
            'status'            => 'required'*/
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



