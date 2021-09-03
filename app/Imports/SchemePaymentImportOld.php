<?php

namespace App\Imports;

use App\Models\Payment\IrriSchemePayment;
use Maatwebsite\Excel\Concerns\ToModel;

class SchemePaymentImportOld implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        if ($row['master_payment'] && $row['organization'] && $row['farmer'] && $row['scheme_application'])
        return new IrriSchemePayment([
            'master_payment_id' => $row['master_payment'],
            'org_id' => $row['organization'],
            'farmer_id' => $row['farmer'],
            'scheme_application_id' => $row['scheme_application'],
            'payment_type_id' => $row['payment_type'],
            'scheme_type_id' => $row['scheme_type'],
            'scheme_participation_fee_id' => $row['scheme_participation_fee'],
            'scheme_security_money_id' => $row['scheme_security_money'],
            'circle_area_id' => $row['circle_area'],
            'amount' => $row['amount'],
            'trnx_currency' => $row['trnx_currency'],
            'mac_addr' => $row['mac_address'],
            'transaction_no' => $row['transaction_no'],
            'status' => 1,
//            'pay_status' => 1,
        ]);
    }
}
