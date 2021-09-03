<?php
namespace App\Library;

use DB;

class SmsLibrary
{
  private $api_token = "SystechNon-c7a532e7-b2db-47a4-98c4-48b5d5bbcf3b";
  private $sid = "SystechNon";
  private $domain = "https://smsplus.sslwireless.com";

  public function sms_helper($sms_info=[])
  {
      $mobile = $sms_info['mobile'];
      $message = $sms_info['message'];
      $csmsId = 'fdklfdfl';
      $this->singleSms($mobile, $message, $csmsId);
  }

  public function singleSms($msisdn, $messageBody, $csmsId)
  {
      $params = [
          "api_token" => $this->api_token,
          "sid" => $this->sid,
          "msisdn" => $msisdn,
          "sms" => $messageBody,
          "csms_id" => $csmsId
      ];
      $url = trim($this->domain, '/')."/api/v3/send-sms";
      $params = json_encode($params);
      $this->callApi($url, $params);
  }

  public function callApi($url, $params)
  {

    try {

      $ch = curl_init(); // Initialize cURL
      curl_setopt($ch, CURLOPT_URL, $url);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
      curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
          'Content-Type: application/json',
          'Content-Length: ' . strlen($params),
          'accept:application/json'
      ));
      $response = curl_exec($ch);
      curl_close($ch);

      return response([
        'success' => true,
        'message' => $response,
        'errors' => []
      ]);
    } catch (Exception $e) {
        return response([
            'success' => false,
            'message' => "Error occurred during communicating with other dependent service." . $ex->getMessage(),
            'errors' => []
        ]);
    }
  }
}