<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class InputexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\InputException::class, get_class($testObject));
        $sStringOut = $testObject->getString(); // (string)$testObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains('InputException', $sStringOut);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxInputException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
