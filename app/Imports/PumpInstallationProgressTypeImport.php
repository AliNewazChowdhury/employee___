<?php

namespace App\Imports;

use App\Models\Config\MasterPumpInstallationProgressType;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class PumpInstallationProgressTypeImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterPumpInstallationProgressType([
            'org_id' => $row['organization'],
            'pump_type_id' => $row['pump_type'],
            'application_type_id' => $row['application_type'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
