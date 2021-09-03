<?php

use App\Http\Controllers\TaskManagement\TaskAssignTasksController;
use Illuminate\Support\Facades\Route;
/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
//Testing
$router->get('/generate-base64-font', "ExampleController@generateBase64Font");
//Interservice communication
$router->get('/test-api', function () {
    $baseUrl = config('app.base_url.auth_service');
    $uri = '/testing-api';
    $uri2 = '/user/detail';
    $param = ['test_param1' => ['1', 2], 'test_param2' => 'test2'];
    return \App\Library\RestService::getData($baseUrl, $uri2, $param);
});

$router->post('/test-api', function () {
    $baseUrl = config('app.base_url.auth_service');
    $uri = '/testing-api';
    $uri2 = '/user/detail';
    $formData = ['test_input1' => ['1', 2], 'test_input2' => 'test2'];
    return \App\Library\RestService::postData($baseUrl, $uri, $formData);
});

$router->put('/test-api', function () {
    $baseUrl = config('app.base_url.auth_service');
    $uri = '/testing-api';
    $uri2 = '/user/detail';
    $formData = ['test_input1' => ['1', 2], 'test_input2' => 'test2'];
    return \App\Library\RestService::putData($baseUrl, $uri, $formData);
});
$router->delete('/test-api/{id}', function ($id) {
    $baseUrl = config('app.base_url.auth_service');
    $uri = "/testing-api/{$id}";
    $formData = ['test_input1' => ['1', 2], 'test_input2' => 'test2'];
    return \App\Library\RestService::deleteData($baseUrl, $uri, $formData);
});

$router->get('/', function () use ($router) {
    return $router->app->version();
});
// for pdf
Route::get('/report/pdf-image', function (Illuminate\Http\Request $request) {
    $result = \App\Library\PdfImage::getBase64Image($request);

    return response($result);
});

/******************** Data Archive Module *********************/
Route::group(['prefix'=>'/data-archive'], function() {
    Route::get('/database-backup', 'DataArchiveController@dumpDB');
    //download file path from storage
    Route::get('download-backup-db', 'DataArchiveController@downloadBackupDb');
    Route::get('db-backup-files', 'DataArchiveController@getDbBackupFiles');
    Route::delete('db-backup-delete', 'DataArchiveController@deleteDbBackupFile');
});

//download file path from storage
Route::get('download-attachment', 'DownloadController@downloadAttachment');


//Master Circle Area Routes
Route::group(['prefix'=>'/master-circle-area'], function(){
    Route::get('/list', 'Config\MasterCircleAreaController@index');
    Route::get('/getList', 'Config\MasterCircleAreaController@getlist');
    Route::post('/store', 'Config\MasterCircleAreaController@store');
    Route::put('/update/{id}', 'Config\MasterCircleAreaController@update');
    Route::delete('/toggle-status/{id}', 'Config\MasterCircleAreaController@toggleStatus');
    Route::delete('/destroy/{id}', 'Config\MasterCircleAreaController@destroy');
});
//employee Routes
Route::group(['prefix'=>'/employee'], function(){
    Route::get('/list', 'Config\EmployeeController@index');
    Route::post('/create', 'Config\EmployeeController@store');
    Route::put('/update/{id}', 'Config\EmployeeController@update');
    Route::delete('/destroy/{id}', 'Config\EmployeeController@delete');
});
