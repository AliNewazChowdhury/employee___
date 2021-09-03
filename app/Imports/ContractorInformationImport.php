<?php

namespace App\Imports;

use App\Models\Config\MasterContractor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ContractorInformationImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new MasterContractor([
            'org_id' => $row['organization'],
            'contractor_name' => $row['contractor_name_en'],
            'contractor_name_bn' => $row['contractor_name_bn'],
            'phone_no' => $row['mobile_number'],
            'address' => $row['contractor_address_en'],
            'address_bn' => $row['contractor_address_bn'],
            'created_by' => 1,
            'updated_by' => 1,
            'status' => 0
        ]);
    }
}
