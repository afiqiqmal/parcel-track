<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

require_once __DIR__ .'/../vendor/autoload.php';

$response = parcel_track()->abxExpress()->setTrackingNumber("EZP843055940197")->fetch();

header('Content-type: application/json');
echo json_encode($response, JSON_PRETTY_PRINT);
