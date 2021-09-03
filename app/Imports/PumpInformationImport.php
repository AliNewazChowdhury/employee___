<?php

namespace App\Imports;

use App\Models\PumpInfoManagement\PumpInfo;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class PumpInformationImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // return 'Hello';
        if ($row['organization']) {
            return new PumpInfo([
                'org_id' => $row['organization'],
                'pump_id' => $row['pump_id'],
                'project_id' => $row['project_id'],
                'division_id' => $row['division_id'],
                'district_id' => $row['district_id'],
                'upazilla_id' => $row['upazilla_id'],
                'union_id' => $row['union_id'],
                'mouza_no' => $row['mouza_no'],
                'jl_no' => $row['jl_no'],
                'plot_no' => $row['plot_no'],
                'water_group_id' => $row['water_group_id'],
                'total_farmer' => $row['total_farmer'],
                'added_farmer' => $row['added_farmer'],
                'latitude' => $row['latitude'],
                'longitude' => $row['longitude'],
                'type_id' => $row['type_id'],
                'created_by' => 1,
                'updated_by' => 1,
                'status' => 0
            ]);
        }
    }
}
