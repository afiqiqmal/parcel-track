<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
/**
* RequestTest.php
* to test function in Request class
*/
class LELTest extends TestCase
{
    function testLELSuccess()
    {
        $result = parcel_track()->lelExpress()->setTrackingNumber("MYMP000000573505")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testLELEmptySuccess()
    {
        $result = parcel_track()->lelExpress()->setTrackingNumber("MYMP000000573505AAAAA")->fetch();

        $this->assertTrue(count($result['tracker']['checkpoints']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testLELFailed()
    {
        $result = parcel_track()->setTrackingNumber("MYMP000000573505")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }
}