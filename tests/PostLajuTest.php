<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use Afiqiqmal\ParcelTrack\Tracker\PosLaju;
use PHPUnit\Framework\TestCase;

/**
* RequestTest.php
* to test function in Request class
*/
class PostLajuTest extends TestCase
{
    function testPostLajuSuccess()
    {
        $result = parcel_track()->postLaju()->setTrackingNumber("ER157080065MY")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testPostLajuEmptySuccess()
    {
        $result = parcel_track()->postLaju()->setTrackingNumber("ER157080065MYAAAAA")->fetch();

        $this->assertTrue(count($result['tracker']['checkpoints']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testPostLajuFailed()
    {
        $result = parcel_track()->setTrackingNumber("ER157080065MY")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }

    function testPostLajuCheckCarrier()
    {
        $result = parcel_track()->setTrackingNumber("ER157080065MY")->checkCourier();
        $this->assertFalse($result['error']);
        $this->assertTrue(in_array((new PosLaju())->getSourceName(), $result['possible_courier']));
    }
}