<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class ShopexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    // We check on class name (exception class) and message only - rest is not checked yet
    public function testGetString()
    {
        $message = 'Erik was here..';
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\ShopException::class, $message);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\ShopException::class, get_class($testObject));
        $stringOut = $testObject->getString(); // (string)$testObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($message, $stringOut);
        $this->assertContains('ShopException', $stringOut);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxShopException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
