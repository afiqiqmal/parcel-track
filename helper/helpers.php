<?php

use Afiqiqmal\Library\ApiRequest;
use Afiqiqmal\Library\ParcelUtils;
use Afiqiqmal\ParcelTrack\ParcelTrack;

define('PARCEL_METHOD_POST', 'POST');
define('PARCEL_METHOD_GET', 'GET');
define('PARCEL_METHOD_PATCH', 'PATCH');
define('PARCEL_METHOD_DELETE', 'DELETE');
define('PARCEL_USER_AGENT', 'testing/1.0');

if (! function_exists('parcel_track')) {

    function parcel_track()
    {
        return new ParcelTrack();
    }
}

if (! function_exists('parcel_request')) {

    function parcel_request()
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