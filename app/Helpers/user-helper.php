<?php
if (!function_exists('user_id')) {
  function user_id()
  {
    return app('request')->header('accessUserId');
  }
}

if (!function_exists('username')) {
  function username()
  {
    return app('request')->header('accessUsername');
  }
}

function getAuthUser()
{
    $baseUrl = config('app.base_url.auth_service');
    $uri2 = '/auth-user';
    $param = [];
    $authUserJsonData = \App\Library\RestService::getData($baseUrl, $uri2, $param);

    $authUser = json_decode($authUserJsonData);

    return $authUser ? $authUser->data : false;
}