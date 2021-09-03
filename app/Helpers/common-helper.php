<?php

use App\Library\RestService;

/**
 * Create offcie
 *
 */
function createLaboratoryOffice($formData)
{  
    $baseUrl = config('app.base_url.common_service');
    $uri = '/master-office/store';

    $responseJson = RestService::postData($baseUrl, $uri, $formData);

    $responseJsonObj = json_decode($responseJson);

    if (!$responseJsonObj->success) {
        return (object)[
            'success' => false,
            'message' => $responseJsonObj->message
        ];
    }

    return (object)[
        'success' => true,
        'data' => $responseJsonObj->data
    ];
}

/**
 * Update Offcie
 *
 */
function updateLaboratoryOffice($formData, $id)
{  
    $baseUrl = config('app.base_url.common_service');
    $uri = '/master-office/store';

    $responseJson = RestService::postData($baseUrl, $uri, $formData);

    $responseJsonObj = json_decode($responseJson);

    if (!$responseJsonObj->success) {
        return (object)[
            'success' => false,
            'message' => $responseJsonObj->message
        ];
    }

    return (object)[
        'success' => true,
        'data' => $responseJsonObj->data
    ];
}
