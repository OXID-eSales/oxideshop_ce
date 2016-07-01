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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Core;

use Exception;
use OxidEsales\Eshop\Core\Registry;
use oxSystemComponentException;
use oxTestModules;
use Psr\Log\NullLogger;
class ExceptionhandlerTest extends \OxidTestCase
{

    protected $_sMsg = 'TEST_EXCEPTION';

    public function testCallUnExistingMethod()
    {
        $this->setExpectedException('oxSystemComponentException');
        $oExcpHandler = oxNew('oxexceptionhandler');
        $oExcpHandler->__test__();
    }

    
    // still incomplete
    // We can only test if a log file is written - screen output must be checked manually or with selenium
    public function testExceptionHandlerNotRendererDebug()
    {
        
        $oExc = oxNew('oxexception', $this->_sMsg);
        $oTestObject = oxNew('oxexceptionhandler', '1'); // iDebug = 1
        $logger = $this->getMock('Psr\Log\NullLogger',['error']);
        
        $logger->expects($this->once())->method('error');
        $oExc->setLogger($logger);
        
        $this->expectShowMessageAndExit();
       
        $sMsg = $oTestObject->handleUncaughtException($oExc); // actual test
        $this->assertNotEquals($this->_sMsg, $sMsg);
        
       
    }

    // We can only test if a log file is not written - screen output must be checked manually or with selenium
    public function testExceptionHandlerNotRendererNoDebug()
    {
        $this->expectOffline();
        $oExc = oxNew('oxexception', $this->_sMsg);
        $oTestObject = oxNew('oxexceptionhandler');            
        $oTestObject->handleUncaughtException($oExc); // actual test
    }

    private function expectShowMessageAndExit()
    {
        $this->expectUtilsMethod('showMessageAndExit');
    }

    public function testExceptionHandlerNotRendererDebugNotOxidException()
    {
       
        $oTestObject = oxNew('oxexceptionhandler', '1'); // iDebug = 1
        $this->expectShowMessageAndExit();

        $oTestObject->handleUncaughtException(new Exception("test exception"));      
    }

    private function expectOffline()
    {
        $this->expectUtilsMethod('redirectOffline');
    }

    private function expectUtilsMethod($methodName)
    {
        /** @var oxUtils|PHPUnit_Framework_MockObject_MockObject $utilsMock */
        $utilsMock = $this->getMock('oxUtils', array($methodName));
        $utilsMock->expects($this->once())->method($methodName);
        Registry::set('oxUtils', $utilsMock);
    }


    public function testSetIDebug()
    {
        $oTestObject = $this->getProxyClass("oxexceptionhandler");
        $oTestObject->setIDebug(2);
        //nothing should happen in unittests
        $this->assertEquals(2, $oTestObject->getNonPublicVar('_iDebug'));
    }

    public function testDealWithNoOxException()
    {
        $oTestObject = oxNew("oxexceptionhandler",'-1');
        $this->expectShowMessageAndExit();       
        $oTestException = new Exception("testMsg");       
        $oTestObject->UNITdealWithNoOxException($oTestException);
    }

}
