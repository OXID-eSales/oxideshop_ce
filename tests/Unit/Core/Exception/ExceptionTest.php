<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Exception;

use OxidEsales\Eshop\Core\Exception\StandardException;

class ExceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    // 1. testing constructor works .. ok, its a pseudo test ;-)
    public function testConstruct()
    {
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class);
        $this->assertInstanceOf(\OxidEsales\Eshop\Core\Exception\StandardException::class, $testObject);
    }

    // 2. testing constructor with message.
    public function testConstructWithMessage()
    {
        $messsage = 'Erik was here..';
        $testObject = oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class, $messsage);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\StandardException::class, get_class($testObject));
        $this->assertTrue($testObject->getMessage() === $messsage);
    }

    // Test log file output
    public function testDebugOut()
    {
        $message = 'Erik was here..';
        $testObject = oxNew(StandardException::class, $message);

        try {
            $testObject->debugOut(); // actuall test
        } catch (Exception $e) {
            // Lets try to delete an eventual left over file
            unlink(OX_LOG_FILE);
            $this->fail();

            return;
        }
        $file = file_get_contents(OX_LOG_FILE);
        file_put_contents(OX_LOG_FILE, '');

        $this->assertContains($message, $file);
    }

    // Test set & get message
    public function testSetMessage()
    {
        $message = 'Erik was here..';
        $testObject = oxNew('oxException');
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\StandardException::class, get_class($testObject));
        $testObject->setMessage($message);
        $this->assertTrue($testObject->getMessage() === $message);
    }

    public function testSetIsRenderer()
    {
        $testObject = oxNew('oxException');
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\StandardException::class, get_class($testObject));
        $testObject->setRenderer();
        $this->assertTrue($testObject->isRenderer());
    }

    public function testSetIsNotCaught()
    {
        $testObject = oxNew('oxException');
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\StandardException::class, get_class($testObject));
        $testObject->setNotCaught();
        $this->assertTrue($testObject->isNotCaught());
    }

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $message = 'Erik was here..';
        $testObject = oxNew('oxException', $message);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\StandardException::class, get_class($testObject));
        $testObject->setRenderer();
        $testObject->setNotCaught();
        $sStringOut = $testObject->getString(); // (string)$oTestObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($message, $sStringOut);
        $this->assertContains('oxException', $sStringOut);
    }

    public function testGetValues()
    {
        $testObject = oxNew('oxException');
        $result = $testObject->getValues();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
