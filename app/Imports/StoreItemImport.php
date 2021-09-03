<?php

namespace App\Imports;

use App\Models\Config\MasterItems;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\ToModel;

class StoreItemImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterItems([
            'org_id' => $row['organization'],
            'category_id' => $row['category'],
            'sub_category_id' => $row['sub_category'],
            'measurement_unit_id' => $row['measurement_unit'],
            'item_name' => $row['item_en'],
            'item_name_bn' => $row['item_bn'],
            'specification' => $row['specification_en'],
            'specification_bn' => $row['specification_bn'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
