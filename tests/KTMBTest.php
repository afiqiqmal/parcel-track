<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use Afiqiqmal\ParcelTrack\Tracker\DHL;
use PHPUnit\Framework\TestCase;

/**
* RequestTest.php
* to test function in Request class
*/
class KTMBTest extends TestCase
{
    function testKTMBSuccess()
    {
        $result = parcel_track()->dhlExpress()->setTrackingNumber("103154269")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testKTMBEmptySuccess()
    {
        $result = parcel_track()->dhlExpress()->setTrackingNumber("103154269AAAA")->fetch();

        $this->assertTrue(count($result['tracker']['checkpoints']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testKTMBFailed()
    {
        $result = parcel_track()->setTrackingNumber("103154269")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }

    function testKTMBCheckCarrier()
    {
        $result = parcel_track()->setTrackingNumber("103154269")->checkCourier();
        $this->assertFalse($result['error']);
        $this->assertTrue(in_array((new DHL())->getSourceName(), $result['possible_courier']));
    }
}