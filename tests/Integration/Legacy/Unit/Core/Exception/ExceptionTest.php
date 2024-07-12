<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Exception;

use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\TestingLibrary\UnitTestCase;

class ExceptionTest extends \PHPUnit\Framework\TestCase
{
    public function testConstruct(): void
    {
        $testObject = oxNew(StandardException::class);
        $this->assertInstanceOf(StandardException::class, $testObject);
    }

    public function testConstructWithMessage(): void
    {
        $message = 'Erik was here..';
        $testObject = oxNew(StandardException::class, $message);
        $this->assertEquals(StandardException::class, $testObject::class);
        $this->assertTrue($testObject->getMessage() === $message);
    }

    public function testSetMessage(): void
    {
        $message = 'Erik was here..';
        $testObject = oxNew('oxException');
        $this->assertEquals(StandardException::class, $testObject::class);
        $testObject->setMessage($message);
        $this->assertTrue($testObject->getMessage() === $message);
    }

    public function testSetIsRenderer(): void
    {
        $testObject = oxNew('oxException');
        $this->assertEquals(StandardException::class, $testObject::class);
        $testObject->setRenderer();
        $this->assertTrue($testObject->isRenderer());
    }

    public function testSetIsNotCaught(): void
    {
        $testObject = oxNew('oxException');
        $this->assertEquals(StandardException::class, $testObject::class);
        $testObject->setNotCaught();
        $this->assertTrue($testObject->isNotCaught());
    }

    public function testGetString(): void
    {
        $message = uniqid('some-message-', true);
        $testObject = oxNew('oxException', $message);
        $this->assertEquals(StandardException::class, $testObject::class);
        $testObject->setRenderer();
        $testObject->setNotCaught();

        $out = $testObject->getString();
        $this->assertStringContainsString($message, $out);
        $this->assertStringContainsString(__FUNCTION__, $out);
    }

    public function testGetValues(): void
    {
        $testObject = oxNew('oxException');
        $result = $testObject->getValues();
        $this->assertEquals(0, count($result));
    }

    public function testGetType(): void
    {
        $class = 'oxException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
