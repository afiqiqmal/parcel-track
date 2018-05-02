<?php
namespace Tests;

require_once __DIR__ .'/../vendor/autoload.php';

use PHPUnit\Framework\TestCase;
/**
* RequestTest.php
* to test function in Request class
*/
class AbxTest extends TestCase
{
    function testAbxSuccess()
    {
        $result = parcel_track()->abxExpress()->setTrackingNumber("EZP843055940197")->fetch();

        $this->assertTrue(true);
        $this->assertEquals(200, $result['code']);
    }

    function testAbxEmptySuccess()
    {
        $result = parcel_track()->abxExpress()->setTrackingNumber("")->fetch();

        $this->assertTrue(count($result['tracker']) == 0);
        $this->assertEquals(200, $result['code']);
    }

    function testAbxFailed()
    {
        $result = parcel_track()->setTrackingNumber("EZP843055940197")->fetch();
        $this->assertTrue($result['error']);
        $this->assertEquals(400, $result['code']);
    }
}