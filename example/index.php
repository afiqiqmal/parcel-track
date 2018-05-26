<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ .'/../vendor/autoload.php';

//$response = parcel_track()->gdExpress()->setTrackingNumber("4941410530")->fetch();
//$response = parcel_track()->skynet()->setTrackingNumber("238216506684")->fetch();
//$response = parcel_track()->abxExpress()->setTrackingNumber("EZP843055940197")->fetch();
//$response = parcel_track()->dhlExpress()->setTrackingNumber("5176011131")->fetch();
//$response = parcel_track()->cityLink()->setTrackingNumber("960307804711915")->fetch();
//$response = parcel_track()->fedEx()->setTrackingNumber("435171366301")->fetch();
//$response = parcel_track()->postLaju()->setTrackingNumber("ER287051644MY")->fetch();
//$response = parcel_track()->lelExpress()->setTrackingNumber("MYMP000000573505")->fetch();
//$response = parcel_track()->postLaju()->setTrackingNumber("ER287051644MY")->fetch();
//$response = parcel_track()->lelExpress()->setTrackingNumber("MYMP000000573505")->fetch();
//$response = parcel_track()->dhlECommerce()->setTrackingNumber("5218031053514008AAAA")->fetch();
//$response = parcel_track()->ktmd()->setTrackingNumber("103154269")->fetch();
$response = parcel_track()->ups()->setTrackingNumber("1Z0V255F0498628539")->fetch();

//$response = parcel_track()->setTrackingNumber("EZP843055940197")->checkCourier();

header('Content-type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
