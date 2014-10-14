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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxexceptionhandlerTest extends OxidTestCase
{
    protected $_sMsg = 'TEST_EXCEPTION';

    public function testCallUnExistingMethod()
    {
        try {
            $oExcpHandler = new oxexceptionhandler();
            $oExcpHandler->__test__();
        } catch ( oxSystemComponentException $oExcp ) {
            return;
        }
        $this->fail('exception must be thrown');
    }

    public function testSetGetFileName()
    {
        $oTestObject = oxNew('oxexceptionhandler');
        $oTestObject->setLogFileName('TEST.log');
        $this->assertEquals('TEST.log', $oTestObject->getLogFileName());
    }

    // still incomplete
    // We can only test if a log file is written - screen output must be checked manually or with selenium
    public function testExceptionHandlerNotRendererDebug()
    {
        $sFileName = 'oxexceptionhandlerTest_NotRenderer.txt';
        $oExc = oxNew('oxexception', $this->_sMsg);
        $oTestObject = oxNew('oxexceptionhandler', '1');  // iDebug = 1
        $oTestObject->setLogFileName($sFileName);

        try {
            $sMsg = $oTestObject->handleUncaughtException($oExc); // actuall test
            $this->assertNotEquals($this->_sMsg, $sMsg);
        } catch (Exception $e) {
            // Lets try to delete an possible left over file
            if (file_exists(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName)) {
                unlink(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName);
            }
            $this->fail('handleUncaughtException() throws an exception.');
            return;
        }
        if (!file_exists(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName)) {
             $this->fail('No debug log file written');
        }
        $sFile = file_get_contents(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName);
        unlink(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName); // delete file first as assert may return out this function
        // we check on class name and message - rest is not checked yet
        $this->assertContains($this->_sMsg, $sFile);
        $this->assertContains('oxException', $sFile);
    }

    // We can only test if a log file is not written - screen output must be checked manually or with selenium
    public function testExceptionHandlerNotRendererNoDebug()
    {
        $sFileName = 'oxexceptionhandlerTest_NotRenderer.txt';
        $oExc = oxNew('oxexception', $this->_sMsg);
        $oTestObject = oxNew('oxexceptionhandler');
        $oTestObject->setLogFileName($sFileName);

        try {
            $oTestObject->handleUncaughtException($oExc); // actuall test
        } catch (Exception $e) {
            // Lets try to delete an possible left over file
            if (file_exists($sFileName)) {
                unlink($sFileName);
            }
            $this->fail('handleUncaughtException() throws an exception.');
            return;
        }
        if (file_exists($sFileName)) {
            $this->fail('Illegally written in log file.');
            @unlink($sFileName); // delete file first as assert may return out this function
        }
    }

    public function testExceptionHandlerNotRendererDebugNotOxidException()
    {
        $sFileName = 'oxexceptionhandlerTest_NotRenderer.txt';
        $oTestObject = oxNew('oxexceptionhandler', '1');  // iDebug = 1
        $oTestObject->setLogFileName($sFileName);

        try {
            throw new Exception("test exception");
             // actuall test
        } catch (Exception $e) {
            $oTestObject->handleUncaughtException($e);
            if (!file_exists(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName)) {
                 $this->fail('No debug log file written');
            }
            $sFile = file_get_contents(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName);
            unlink(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName); // delete file first as assert may return out this function
            $this->assertContains("test exception", $sFile);
            $this->assertContains('Exception', $sFile);
            return;
        }
        if (file_exists(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName)) {
            unlink(oxConfig::getInstance()->getConfigParam( 'sShopDir' ).'log/'.$sFileName);
        }
        $this->fail('Test failed.');
    }

    public function testSetIDebug()
    {
        $oTestObject = $this->getProxyClass( "oxexceptionhandler" );
        $oTestObject->setIDebug(2);
        //nothing should happen in unittests
        $this->assertEquals(2, $oTestObject->getNonPublicVar('_iDebug'));
    }

    public function testDealWithNoOxException()
    {
        $oTestObject = $this->getProxyClass( "oxexceptionhandler" );
        $oTestObject->setIDebug(-1);

        $oTestUtils = $this->getMock("oxUtils", array("writeToLog", "showMessageAndExit", "getTime"));
        $oTestUtils->expects($this->once())->method("writeToLog");
        $oTestException = new Exception("testMsg");

        oxTestModules::addModuleObject( 'oxUtils', $oTestUtils );

        try {
            $oTestObject->UNITdealWithNoOxException($oTestException);
        } catch(Exception $e) {

        }
    }

}
