<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Models\PumpMaintenance\PumpDirectories;
use App\Models\PumpMaintenance\PumpDirectoryEquipments;
use Maatwebsite\Excel\Concerns\ToModel;

class MechanicInformationImport implements ToModel, WithHeadingRow
{
    /**
    * @param Collection $collection
    */
    public function model(array $row)
    {
        if ($row['mobile'] && $row['name'] && $row['email'] && $row['division_id']) {

            $pumpDirectory =  new PumpDirectories([
                'type_id' => 3,
                'name' => $row['name'],
                'name_bn' => $row['name_bn'],
                'village_name' => $row['village'],
                'village_name_bn' => $row['village_bn'],
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
            $pumpDirectory->save();

            $total_equip= explode('/', $row['equipment']);

            foreach ($total_equip as $equip) {
                $data= explode(',', $equip);
                if ($data[0] && $data[1] &&$data[2]) {
                    $directoryEquipment =  new PumpDirectoryEquipments([
                        'pump_directory_id' => $pumpDirectory->id,
                        'master_equipment_type_id' => $data[0],
                        'details' => $data[1],
                        'details_bn' => $data[2]
                    ]);
                    $directoryEquipment->save();
                }
            }

            return $pumpDirectory;
        }
    }
}
