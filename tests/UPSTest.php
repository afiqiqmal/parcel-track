<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use afiqiqmal\ParcelTrack\Tracker\UPS;
use PHPUnit\Framework\TestCase;
/**
* RequestTest.php
* to test function in Request class
*/
class UPSTest extends TestCase
{
    function testUPSSuccess()
    {
        $result = parcel_track()->ups()->setTrackingNumber("1Z0V255F0498628539")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testUPSEmptySuccess()
    {
        $result = parcel_track()->ups()->setTrackingNumber("")->fetch();

        $this->assertTrue(count($result['tracker']['checkpoints']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testUPSFailed()
    {
        $result = parcel_track()->setTrackingNumber("1Z0V255F04986285")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }

    function testUPSCheckCarrier()
    {
        $result = parcel_track()->setTrackingNumber("1Z0V255F0498628539")->checkCourier();
        $this->assertFalse($result['error']);
        $this->assertTrue(in_array((new UPS())->getSourceName(), $result['possible_courier']));
    }
}