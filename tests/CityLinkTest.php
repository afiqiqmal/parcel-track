<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use Afiqiqmal\ParcelTrack\Tracker\CityLink;
use PHPUnit\Framework\TestCase;

/**
* RequestTest.php
* to test function in Request class
*/
class CityLinkTest extends TestCase
{
    function testCityLinkSuccess()
    {
        $result = parcel_track()->cityLink()->setTrackingNumber("960307804711915")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testCityLinkEmptySuccess()
    {
        $result = parcel_track()->cityLink()->setTrackingNumber("")->fetch();

        $this->assertTrue(count($result['tracker']['checkpoints']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testCityLinkFailed()
    {
        $result = parcel_track()->setTrackingNumber("960307804711915")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }

    function testCityLinkCheckCarrier()
    {
        $result = parcel_track()->setTrackingNumber("960307804711915")->checkCourier();
        $this->assertFalse($result['error']);
        $this->assertTrue(in_array((new CityLink())->getSourceName(), $result['possible_courier']));
    }
}