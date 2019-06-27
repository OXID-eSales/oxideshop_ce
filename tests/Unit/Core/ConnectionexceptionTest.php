<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Class ConnectionexceptionTest
 *
 * @group database-adapter
 */
class ConnectionexceptionTest extends \OxidTestCase
{
    public function testSetGetAddress()
    {
        $sAddress = 'sServerAddress';
        $oTestObject = oxNew('oxConnectionException');
        $oTestObject->setAdress($sAddress);
        $this->assertEquals($sAddress, $oTestObject->getAdress());
    }

    public function testSetGetConnectionError()
    {
        $sConnectionError = 'sSomeConnectionError';
        $oTestObject = oxNew('oxConnectionException');
        $oTestObject->setConnectionError($sConnectionError);
        $this->assertEquals($sConnectionError, $oTestObject->getConnectionError());
    }

    // We check on class name and message only - rest is not checked yet
    public function testSetString()
    {
        $sMsg = 'Erik was here..';
        $oTestObject = oxNew('oxConnectionException', $sMsg);
        $sAddress = 'sServerAddress';
        $oTestObject->setAdress($sAddress);
        $sConnectionError = 'sSomeConnectionError';
        $oTestObject->setConnectionError($sConnectionError);
        $sStringOut = $oTestObject->getString();
        $this->assertContains($sMsg, $sStringOut); // Message
        $this->assertContains('ConnectionException', $sStringOut); // Exception class name
        $this->assertContains($sAddress, $sStringOut); // Server Address
        $this->assertContains($sConnectionError, $sStringOut); // Connection error
    }

    public function testGetValues()
    {
        $oTestObject = oxNew('oxConnectionException');
        $sAddress = 'sServerAddress';
        $oTestObject->setAdress($sAddress);
        $sConnectionError = 'sSomeConnectionError';
        $oTestObject->setConnectionError($sConnectionError);
        $aRes = $oTestObject->getValues();
        $this->assertArrayHasKey('adress', $aRes);
        $this->assertTrue($sAddress === $aRes['adress']);
        $this->assertArrayHasKey('connectionError', $aRes);
        $this->assertTrue($sConnectionError === $aRes['connectionError']);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxConnectionException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
