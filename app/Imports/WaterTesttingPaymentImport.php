<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\Config\MasterPayment;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class WaterTesttingPaymentImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new MasterPayment([
            'org_id' => (int)$row['organization'],
            'application_type_id' => (int)$row['application_type'],
            'payment_type_id' => (int)($row['payment_type']) ? $row['payment_type'] : 0,
            'scheme_type_id' => (int)(isset($row['scheme_type'])) ? $row['scheme_type'] : 0,
            'amount' => $row['amount'],
            'effective_from' => $row['effective_from'],
            'participation_category_id' => (int)(isset($row['participation_category'])) ? $row['participation_category'] : 0,
            'pump_type_id' => (int)(isset($row['pump_type'])) ? $row['pump_type'] : 0,
            'discharge_cusec' => (int)(isset($row['discharge_cusec'])) ? $row['discharge_cusec'] : 0,
            'circle_area_id' => (int)(isset($row['circle_area'])) ? $row['circle_area'] : 0,
            'gender' => (int)(isset($row['gender'])) ? $row['gender'] : 0
        ]);
    }
}
