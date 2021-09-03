<?php

namespace App\Imports;

use App\Models\Config\MasterSchemeForm;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SchemeFormAffidavitImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterSchemeForm([
            'org_id' => $row['organization'],
            'affidavit' => $row['affidavit_en'],
            'affidavit_bn' => $row['affidavit_bn'],
            'scheme_type_id' => $row['scheme_type'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
