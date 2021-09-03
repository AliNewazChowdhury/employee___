<?php

namespace App\Imports;

use App\Models\Config\MasterMeasurementUnits;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class MeasurementUnitImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterMeasurementUnits([
            'org_id' => $row['organization'],
            'unit_name' => $row['unit_name_en'],
            'unit_name_bn' => $row['unit_name_bn'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
