<?php
namespace App\Library;

use GuzzleHttp\Client;
use DB;

class RestConfig
{
  private $authorization = null;
  private $accessUserId = null;
  private $guzzleClient = null;

  public function __construct($baseUrl) {
    $this->authorization = app('request')->header('authorization');
    $this->accessUserId = app('request')->header('accessUserId');
    $this->guzzleClient = new Client(['base_uri' => $baseUrl, 'headers' => [
        'Authorization'   => $this->authorization,
        'accessUserId'    => $this->accessUserId,
        'Accept'          => 'application/json',
      ]
    ]);
  }

  public function getRequest($uri, $param)
  {
    $queryParam = [];

    if (isset($param) && is_array($param)) {
      $queryParam = $param;
    }

    try {
      $res = $this->guzzleClient->request('GET', $uri, ['query' => $queryParam]);
      
      if ($res->getStatusCode() != 200) {
        return response([
          'success' => false,
          'message' => "Failed to get content from other service",
          'errors' => []
        ]);
      }
    } catch (\Exception $ex) {
      return response([
        'success' => false,
        'message' => "Error occurred during communicating with other dependent service." . $ex->getMessage(),
        'errors' => []
      ]);
    }
    
    return $res->getBody()->getContents();
  }

  public function postRequest($uri, $data = [])
  {
    $formParams = [];

    if (!empty($data) && is_array($data)) {
      $formParams = $data;
    }
    
    try {
      $res = $this->guzzleClient->request('POST', $uri, ['form_params' => $formParams]);
      
      if ($res->getStatusCode() != 200) {
        return response([
          'success' => false,
          'message' => "Failed to get content from other service",
          'errors' => []
        ]);
      }
    } catch (\Exception $ex) {
      return response([
        'success' => false,
        'message' => "Error occurred during communicating with other dependent service." . $ex->getMessage(),
        'errors' => []
      ]);
    }
    
    return $res->getBody()->getContents();
  }

  
  public function putRequest($uri, $data = [])
  {
    $formParams = [];

    if (!empty($data) && is_array($data)) {
      $formParams = $data;
    }
    
    try {
      $res = $this->guzzleClient->request('PUT', $uri, ['json' => $formParams]);
      
      if ($res->getStatusCode() != 200) {
        return response([
          'success' => false,
          'message' => "Failed to get content from other service",
          'errors' => []
        ]);
      }
    } catch (\Exception $ex) {
      return response([
        'success' => false,
        'message' => "Error occurred during communicating with other dependent service." . $ex->getMessage(),
        'errors' => []
      ]);
    }
    
    return $res->getBody()->getContents();
  }

  
  public function deleteRequest($uri, $data = [])
  {
    $formParams = [];

    if (!empty($data) && is_array($data)) {
      $formParams = $data;
    }
    
    try {
      $res = $this->guzzleClient->delete($uri, ['json' => $formParams]);
      
      if ($res->getStatusCode() != 200) {
        return response([
          'success' => false,
          'message' => "Failed to get content from other service",
          'errors' => []
        ]);
      }
    } catch (\Exception $ex) {
      return response([
        'success' => false,
        'message' => "Error occurred during communicating with other dependent service." . $ex->getMessage(),
        'errors' => []
      ]);
    }
    
    return $res->getBody()->getContents();
  }
  
}