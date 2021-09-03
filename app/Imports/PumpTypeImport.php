<?php

namespace App\Imports;

use App\Models\Config\PumpType;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class PumpTypeImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new PumpType([
            'org_id' => $row['organization'],
            'pump_type_name' => $row['pump_type_en'],
            'pump_type_name_bn' => $row['pump_type_bn'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
