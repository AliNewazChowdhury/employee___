<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Model\PumpMaintenance\PumpDirectories;
use Maatwebsite\Excel\Concerns\ToModel;

class HardwareShopMechanicInformationImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if ($row['type_id'] && $row['name']) {
            return new PumpDirectories([
                'type_id' => $row['type_id'],
                'name' => $row['name'],
                'name_bn' => $row['name_bn'],
                'village_name' => $row['village_name'],
                'village_name_bn' => $row['village_name_bn'],
                'address' => $row['address'],
                'address_bn' => $row['address_bn'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'mobile' => $row['mobile'],
                'email' => $row['email'],
                'division_id' => $row['division_id'],
                'district_id' => $row['district_id'],
                'upazila_id' => $row['upazila_id'],
                'union_id' => $row['union_id'],
                'created_by' => 1,
                'updated_by' => 1,
                'status' => 0
            ]);
        }
    }
}
