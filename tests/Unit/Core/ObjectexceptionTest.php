<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class ObjectexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testSetGetObject()
    {
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\ObjectException::class);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\ObjectException::class, get_class($testObject));
        $object = new \stdClass();
        $object->sAtrib = "Atribute";
        $testObject->setObject($object);
        $this->assertEquals("Atribute", $testObject->getObject()->sAtrib);
    }

    // We check on class name (exception class) and message only - rest is not checked yet
    public function testGetString()
    {
        $message = 'Erik was here..';
        $testObject = oxNew('oxObjectException', $message);
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\ObjectException', get_class($testObject));
        $object = new \stdClass();
        $testObject->setObject($object);
        $sStringOut = $testObject->getString(); // (string)$oTestObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($message, $sStringOut);
        $this->assertContains('ObjectException', $sStringOut);
        $this->assertContains(get_class($object), $sStringOut);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxObjectException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
