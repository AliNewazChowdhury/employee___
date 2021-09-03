<?php
namespace App\Library;

use Illuminate\Support\Facades\DB;

class DropDowns
{

  public static function schemeTypeList()
  {
    return DB::table('master_scheme_types')
          ->select("id as value",'scheme_type_name as text_en','scheme_type_name_bn as text_bn','org_id', 'status')
          ->get();
  }

  public static function subSchemeTypeList()
  {
    return DB::table('sub_scheme_types')
          ->select("id as value", 'sub_scheme_type_name as text_en', 'sub_scheme_type_name_bn as text_bn', 'org_id', 'master_scheme_type_id', 'status')
          ->get();
  }

  public static function schemeFromAffidavits()
  {
    return DB::table('master_scheme_affidavits')
          ->select("id as value",'title as text_en','title_bn as text_bn','org_id','scheme_type_id','affidavit', 'affidavit_bn', 'status')
          ->get();
  }

  public static function categoryList()
  {
    return DB::table('master_item_categories')
          ->select("id as value",'category_name as text_en','category_name_bn as text_bn','org_id', 'status')
          ->get();
  }

  public static function subCategoryList()
  {
    return DB::table('master_item_sub_categories')
          ->select("id as value",'sub_category_name as text_en','sub_category_name_bn as text_bn','org_id','category_id', 'status')
          ->get();
  }

  public static function unitList()
  {
    return DB::table('master_measurement_units')
          ->select("id as value",'unit_name as text_en','unit_name_bn as text_bn','org_id', 'status')
          ->get();
  }

  public static function itemList()
  {
    return DB::table('master_items')
          ->select("id as value",'item_name as text_en','item_name_bn as text_bn','org_id','category_id','sub_category_id', 'measurement_unit_id', 'status')
          ->get();
  }

  public static function pumpType()
  {
    return DB::table('master_pump_types')
          ->select("id as value",'pump_type_name as text_en','pump_type_name_bn as text_bn','org_id', 'status')
          ->get();
  }

  public static function horsePower()
  {
    return DB::table('master_horse_powers')
          ->select("id as value",'horse_power as text_en','horse_power_bn as text_bn','org_id','pump_type_id', 'status')
          ->get();
  }

  public static function pumpStock()
  {
    return DB::table('pump_current_stocks')
          ->select('id','org_id','office_id','item_id','quantity')
          ->get();
  }

  public static function circleArea()
  {
    return DB::table('master_circle_areas')
          ->select('id as value','org_id','division_id','district_id','circle_area_name as text','circle_area_name as text_en','circle_area_name_bn as text_bn', 'status')
          ->get();
  }

  public static function projectList()
  {
    return DB::table('master_projects')
          ->select('id as value','org_id','project_name as text_en','project_name_bn as text_bn', 'status')
          ->get();
  }

  public static function laboratoryList()
  {
    return DB::table('master_laboratories')
          ->select('id as value','org_id','laboratory_name as text','laboratory_name_bn as text_bn', 'status')
          ->get();
  }

  public static function pumpInfoList()
  {
    return DB::table('pump_informations')->get();
  }

  public static function equipmentTypeList()
  {
    return DB::table('master_equipment_types')
          ->select('id as value','org_id','eq_type_name as text_en','eq_type_name_bn as text_bn', 'status')
          ->get();
  }

  public static function waterTestingParameterList()
  {
    return DB::table('water_testing_parameters')
          ->select('id as value','org_id','testing_type_id','name as text_en','name_bn as text_bn', 'status')
          ->get();
  }

  public static function pumpCapacityList()
  {
    return DB::table('master_pump_capacities')
          ->select('id as value','org_id','master_scheme_type_id','capacity as text_en','capacity as text_bn', 'status')
          ->get();
  }
}
