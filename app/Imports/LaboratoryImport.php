<?php

namespace App\Imports;

use App\Models\Config\MasterLaboratory;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class LaboratoryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if ($row['laboratory_name'] && $row['address'] && $row['organization']) {
            return new MasterLaboratory([
                'laboratory_name' => $row['laboratory_name'],
                'laboratory_name_bn' => $row['laboratory_name_bn'],
                'address' => $row['address'],
                'address_bn' => $row['address_bn'],
                'org_id' => $row['organization'],
                'created_by' => 1,
                'updated_by' => 1,
                'status' => 0
            ]);
        }
    }
}
