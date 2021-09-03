<?php

namespace App\Imports;

use App\Models\Config\MasterItemCategories;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class ItemCategoryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterItemCategories([
            'org_id' => $row['organization'],
            'category_name' => $row['category_name_en'],
            'category_name' => $row['category_name_bn'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
