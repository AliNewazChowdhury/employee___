<?php

namespace App\Imports;

use App\Models\Config\MasterScheme;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class SchemeTypeImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterScheme([
            'org_id' => $row['organization'],
            'scheme_type_name' => $row['scheme_type_en'],
            'scheme_type_name_bn' => $row['scheme_type_bn'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
