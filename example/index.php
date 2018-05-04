<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ .'/../vendor/autoload.php';

//$response = parcel_track()->gdExpress()->setTrackingNumber("4941410530")->fetch();
//$response = parcel_track()->skynet()->setTrackingNumber("238216506684")->fetch();
//$response = parcel_track()->abxExpress()->setTrackingNumber("4941410530")->fetch();
//$response = parcel_track()->dhlExpress()->setTrackingNumber("5176011131")->fetch();
$response = parcel_track()->cityLink()->setTrackingNumber("960307804711915")->fetch();

header('Content-type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
