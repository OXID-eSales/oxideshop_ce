<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class CookieexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $message = 'Erik was here..';
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\CookieException::class, $message);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\CookieException::class, get_class($testObject));
        $stringOut = $testObject->getString();
        $this->assertContains($message, $stringOut); // Message
        $this->assertContains('CookieException', $stringOut); // Exception class name
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxCookieException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
