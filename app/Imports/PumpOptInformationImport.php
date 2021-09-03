<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\PumpOperator\FarmerPumpOperatorApplication;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\PumpInfoManagement\PumpOperator;
use App\Models\FarmerProfile\FarmerBasicInfos;
use Illuminate\Support\Facades\Hash;
use DB;
use Log;

class PumpOptInformationImport implements ToModel, WithHeadingRow
{
   /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
                try {
                    if ($row['mobile_no']) {
        
                        $baseUrl = config('app.base_url.auth_service');
                        $uri = '/register-farmer-from-irrigation';
                        $formData = ['name' => $row['name'],'name_bn' => $row['name_bn'],'mobile_no' => $row['mobile_no'],'username' => $row['mobile_no'],'email' => $row['email'],'password' => Hash::make(123456),'user_type_id' => 1 ];
                        $user = \App\Library\RestService::postData($baseUrl, $uri, $formData);
                        $user_id = json_decode($user, true)['id'];
                        Log::info($user_id);
    
                        DB::beginTransaction();
                        $FarmerBasicInfos =  new FarmerBasicInfos([
                            'farmer_id' => $user_id,
                            'email' => $row['email'],
                            'mobile_no' => $row['mobile_no'],
                            'name' => $row['name'],
                            'name_bn' => $row['name_bn'],
                            'gender' => $row['gender'],
                            'father_name' => $row['father_name'],
                            'father_name_bn' => $row['father_name_bn'],
                            'mother_name' => $row['mother_name'],
                            'mother_name_bn' => $row['mother_name_bn'],
                            'nid' => $row['nid'],
                            'far_division_id' => $row['division_id'],
                            'far_district_id' => $row['district_id'],
                            'far_upazilla_id' => $row['upazila_id'],
                            'far_union_id' => $row['union_id'],
                            'far_village' => $row['village_name'],
                            'far_village_bn' => $row['village_name_bn'],
                            'created_by' => 1,
                            'updated_by' => 1,
                            'status' => 1
                        ]);
                        $FarmerBasicInfos->save();
                
                        $PumpOperator =  new PumpOperator([
                            'org_id' => $row['organization'],
                            'pump_id' => $row['pump_id'],
                            'email' => $row['email'],
                            'mobile_no' => $row['mobile_no'],
                            'name' => $row['name'],
                            'name_bn' => $row['name_bn'],
                            'gender' => $row['gender'],
                            'father_name' => $row['father_name'],
                            'father_name_bn' => $row['father_name_bn'],
                            'mother_name' => $row['mother_name'],
                            'mother_name_bn' => $row['mother_name_bn'],
                            'nid' => $row['nid'],
                            'village_name' => $row['village_name'],
                            'village_name_bn' => $row['village_name_bn'],
                            'latitude' => $row['latitude'],
                            'longitude' => $row['longitude'],
                            'pump_operator_user_id' => $user_id,
                            'pump_operator_username' => $row['mobile_no'],
                            'pump_operator_email' => $row['email'],
                            'created_by' => 1,
                            'updated_by' => 1,
                            'status' => 0
                        ]);
                        $PumpOperator->save();
                        DB::commit();

                        return $FarmerBasicInfos;
                    }
                } catch (Exception $e) {
                    BD::rollback();
                }
    }
}
