<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use afiqiqmal\ParcelTrack\Tracker\CityLink;
use afiqiqmal\ParcelTrack\Tracker\DHL;
use PHPUnit\Framework\TestCase;
/**
* RequestTest.php
* to test function in Request class
*/
class DHLTest extends TestCase
{
    function testDHLSuccess()
    {
        $result = parcel_track()->dhlExpress()->setTrackingNumber("5176011131")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testDHLEmptySuccess()
    {
        $result = parcel_track()->dhlExpress()->setTrackingNumber("5176011131AAAA")->fetch();

        $this->assertTrue(count($result['tracker']['checkpoints']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testDHLFailed()
    {
        $result = parcel_track()->setTrackingNumber("5176011131")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }

    function testDHLCheckCarrier()
    {
        $result = parcel_track()->setTrackingNumber("5176011131")->checkCarrier();
        $this->assertFalse($result['error']);
        $this->assertTrue(in_array((new DHL())->getSourceName(), $result['possible_carrier']));
    }
}