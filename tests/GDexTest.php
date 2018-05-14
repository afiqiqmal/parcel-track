<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use afiqiqmal\ParcelTrack\Tracker\Gdex;
use PHPUnit\Framework\TestCase;
/**
* RequestTest.php
* to test function in Request class
*/
class GDexTest extends TestCase
{
    function testGDexSuccess()
    {
        $result = parcel_track()->gdExpress()->setTrackingNumber("4941410530")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testGDexEmptySuccess()
    {
        $result = parcel_track()->gdExpress()->setTrackingNumber("4941410530AAAA")->fetch();

        $this->assertTrue(count($result['tracker']['checkpoints']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testGDexFailed()
    {
        $result = parcel_track()->setTrackingNumber("4941410530")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }

    function testGDexCheckCarrier()
    {
        $result = parcel_track()->setTrackingNumber("4941410530")->checkCarrier();
        $this->assertFalse($result['error']);
        $this->assertTrue(in_array((new Gdex())->getSourceName(), $result['possible_carrier']));
    }
}