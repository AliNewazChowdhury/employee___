<?php

namespace App\Imports;

use App\Models\Config\MasterProject;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ProjectInformationImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterProject([
            'project_name' => $row['project_name_en'],
            'project_name_bn' => $row['project_name_bn'],
            'org_id' => $row['organization'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
