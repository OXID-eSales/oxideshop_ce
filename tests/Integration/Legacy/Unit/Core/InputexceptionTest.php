<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class InputexceptionTest extends \PHPUnit\Framework\TestCase
{

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\InputException::class);
        $this->assertSame(\OxidEsales\Eshop\Core\Exception\InputException::class, $testObject::class);
        $sStringOut = $testObject->getString(); // (string)$testObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertStringContainsString('InputException', $sStringOut);
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
