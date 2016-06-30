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
use Psr\Log\NullLogger;
use Psr\Log\LoggerInterface;

class ExceptionTest extends \OxidTestCase
{

    // 1. testing constructor works .. ok, its a pseudo test ;-)
    public function testConstruct()
    {
        $oTestObject = oxNew('oxException');
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\StandardException', get_class($oTestObject));
    }

    // 2. testing constructor with message.
    public function testConstructWithMessage()
    {
        $sMsg = 'Erik was here..';
        $oTestObject = oxNew('oxException', $sMsg);
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\StandardException', get_class($oTestObject));
        $this->assertTrue($oTestObject->getMessage() === $sMsg);
    }


    // Test log file output
    public function testDebugOut()
    {
        $sMsg = 'Erik was here..';
        $oTestObject = oxNew('oxException', $sMsg);
        $logger = $this->getMock('Psr\Log\NullLogger',['error']);
        $this->assertInstanceOf('Psr\Log\NullLogger',$logger);
        $this->assertInstanceOf('Psr\Log\LoggerInterface',$logger);

        $logger->expects($this->once())->method('error')->with($this->matchesRegularExpression("/oxException.*$sMsg/"));
        $oTestObject->setLogger($logger);
        
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\StandardException', get_class($oTestObject));
        $oTestObject->debugOut();               
    }

    // Test set & get message
    public function testSetMessage()
    {
        $sMsg = 'Erik was here..';
        $oTestObject = oxNew('oxException');
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\StandardException', get_class($oTestObject));
        $oTestObject->setMessage($sMsg);
        $this->assertTrue($oTestObject->getMessage() === $sMsg);
    }

    public function testSetIsRenderer()
    {
        $oTestObject = oxNew('oxException');
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\StandardException', get_class($oTestObject));
        $oTestObject->setRenderer();
        $this->assertTrue($oTestObject->isRenderer());
    }

    public function testSetIsNotCaught()
    {
        $oTestObject = oxNew('oxException');
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\StandardException', get_class($oTestObject));
        $oTestObject->setNotCaught();
        $this->assertTrue($oTestObject->isNotCaught());
    }

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $sMsg = 'Erik was here..';
        $oTestObject = oxNew('oxException', $sMsg);
        $this->assertEquals('OxidEsales\Eshop\Core\Exception\StandardException', get_class($oTestObject));
        $oTestObject->setRenderer();
        $oTestObject->setNotCaught();
        $sStringOut = $oTestObject->getString(); // (string)$oTestObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains($sMsg, $sStringOut);
        $this->assertContains('oxException', $sStringOut);
    }

    public function testGetValues()
    {
        $oTestObject = oxNew('oxException');
        $aRes = $oTestObject->getValues();
        $this->assertEquals(0, count($aRes));
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
