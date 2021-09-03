<?php

namespace App\Imports;

use App\Models\Config\MasterItemSubCategories;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class ItemSubCategoryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterItemSubCategories([
            'org_id' => $row['organization'],
            'category_id' => $row['category'],
            'sub_category_name_en' => $row['sub_category_name_en'],
            'sub_category_name_bn' => $row['sub_category_name_bn'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
