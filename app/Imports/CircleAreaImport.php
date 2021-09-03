<?php

namespace App\Imports;

use App\Models\Config\MasterCircleArea;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CircleAreaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterCircleArea([
            'org_id' => $row['organization'],
            'division_id' => $row['division'],
            'district_id' => $row['district'],
            'circle_area_name' => $row['circle_area_name_en'],
            'circle_area_name_bn' => $row['circle_area_name_bn'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
