<?php

namespace App\Models\FarmerOperator;

use Illuminate\Database\Eloquent\Model;

class FarmerSchemeApplicationDetails extends Model
{
    protected $table ="far_scheme_app_details";

    protected $fillable = [
        'farmer_id',	
		'email',	
		'scheme_application_id',	
		'sch_man_name',	
		'sch_man_name_bn',	
		'sch_man_father_name',	
		'sch_man_father_name_bn',	
		'sch_man_mother_name',	
		'sch_man_mother_name_bn',	
		'sch_man_division_id',
		'sch_man_district_id',	
		'sch_man_upazilla_id',	
		'sch_man_union_id',	
		'sch_man_village',	
		'sch_man_village_bn',	
		'sch_man_mobile_no',
		'sch_man_nid',	
		'pump_district_id',	
		'pump_upazilla_id',	
		'pump_union_id',	
		'pump_mauza_no',	
		'pump_mauza_no_bn',	
		'pump_jl_no',	
		'pump_jl_no_bn',
		'pump_plot_no',	
		'pump_plot_no_bn',	
		'command_area_hector',	
		'general_minutes',	
		'scheme_lands',	
		'scheme_map',	
		'affidavit_id',
    ];

}





