<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use afiqiqmal\ParcelTrack\Tracker\SkyNet;
use PHPUnit\Framework\TestCase;
/**
* RequestTest.php
* to test function in Request class
*/
class SkyNetTest extends TestCase
{
    function testSkyNetSuccess()
    {
        $result = parcel_track()->skynet()->setTrackingNumber("238216506684")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testSkyNetEmptySuccess()
    {
        $result = parcel_track()->skynet()->setTrackingNumber("238216506684A")->fetch();

        $this->assertTrue(count($result['tracker']['checkpoints']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testSkyNetFailed()
    {
        $result = parcel_track()->setTrackingNumber("238216506684")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }

    function testSkyNetCheckCarrier()
    {
        $result = parcel_track()->setTrackingNumber("238216506684")->checkCourier();
        $this->assertFalse($result['error']);
        $this->assertTrue(in_array((new SkyNet())->getSourceName(), $result['possible_courier']));
    }
}