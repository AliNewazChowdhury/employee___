<?php
namespace App\Library;

use App\Library\RestConfig;

class RestService
{

  public static function getData($baseUrl, $uri, $param = null)
  {
    return (new RestConfig($baseUrl))->getRequest($uri, $param);
  }

  public static function postData($baseUrl, $uri, $formData = [])
  {
    return (new RestConfig($baseUrl))->postRequest($uri, $formData);
  }

  public static function putData($baseUrl, $uri, $formData = [])
  {
    return (new RestConfig($baseUrl))->putRequest($uri, $formData);
  }

  public static function deleteData($baseUrl, $uri, $formData = [])
  {
    return (new RestConfig($baseUrl))->deleteRequest($uri, $formData);
  }
  
}