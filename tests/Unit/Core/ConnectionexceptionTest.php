<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace Unit\Core;

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
