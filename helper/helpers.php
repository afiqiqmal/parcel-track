<?php

use afiqiqmal\Library\ApiRequest;
use Afiqiqmal\Library\ParcelUtils;
use afiqiqmal\ParcelTrack\ParcelTrack;

define('METHOD_POST', 'POST');
define('METHOD_GET', 'GET');
define('METHOD_PATCH', 'PATCH');
define('METHOD_DELETE', 'DELETE');
define('USER_AGENT', 'testing/1.0');

if (! function_exists('parcel_track')) {

    function parcel_track()
    {
        return new ParcelTrack();
    }
}

if (! function_exists('api_request')) {

    function api_request()
    {
        return new ApiRequest();
    }
}

if (! function_exists('parcel_utils')) {

    function parcel_utils()
    {
        return new ParcelUtils();
    }
}

if (! function_exists('trim_spaces')) {
    function trim_spaces($text)
    {
        return trim(preg_replace('/\s+/', ' ', $text));
    }
}

if (! function_exists('die_response')) {

    function die_response($message = "Something Went Wrong")
    {
        http_response_code(400);
        return [
            'code' => 400,
            'error' => true,
            'message' => $message
        ];
    }
}