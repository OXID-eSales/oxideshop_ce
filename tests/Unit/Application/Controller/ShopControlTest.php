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
namespace Unit\Application\Controller;

use modDB;
use \Exception;
use \oxException;
use \oxConnectionException;
use \OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay;
use \OxidEsales\EshopCommunity\Core\Output;
use oxOutput;
use \oxSystemComponentException;
use \oxRegistry;
use \oxTestModules;

// Force autoloading of Smarty class, so that mocking would work correctly.
class_exists('Smarty');

class ShopControlTest extends \OxidTestCase
{

    protected function tearDown()
    {
        parent::tearDown();

        modDB::getInstance()->cleanup();
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStart()
    {
        $this->setRequestParameter('cl', null);
        $this->setRequestParameter('fnc', "testFnc");

        $oConfig = $this->getMock("oxConfig", array("isMall", "getConfigParam", "getShopHomeUrl"));
        $oConfig->expects($this->any())->method('isMall')->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getShopHomeUrl');

        $oControl = $this->getMock("oxShopControl", array("getConfig", "_runOnce", "isAdmin", "_process"), array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->once())->method('_runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->once())->method('_process')->with($this->equalTo("start"), $this->equalTo("testFnc"));

        $oControl->start();
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStartIsAdmin()
    {
        $this->setRequestParameter('cl', null);
        $this->setRequestParameter('fnc', "testFnc");
        $this->getSession()->setVariable('actshop', null);
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception("Error in testStart()"); }');
        modDB::getInstance()->addClassFunction('getOne', create_function('$x', 'return 2;'));

        $oConfig = $this->getMock("oxConfig", array("getShopHomeUrl"));
        //$oConfig->expects( $this->never() )->method( 'getShopId' )->will( $this->returnValue( 999 ) );
        $oConfig->expects($this->never())->method('getShopHomeUrl');

        $oControl = $this->getMock("oxShopControl", array("getConfig", "_runOnce", "isAdmin", "_process"), array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->once())->method('_runOnce');
        $oControl->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->once())->method('_process')->with($this->equalTo("login"), $this->equalTo("testFnc"));
        $oControl->start();

        //$this->assertEquals( $this->getConfig()->getBaseShopId(), $this->getSession()->getVariable( "actshop" ) );
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStartWithLoggedInAdminAndNoControllerSpecified()
    {
        $this->setRequestParameter('cl', null);
        $this->setRequestParameter('fnc', 'testFnc');
        $this->getSession()->setVariable('auth', true);

        $oControl = $this->getMock("oxShopControl", array("_runOnce", "isAdmin", "_process"), array(), '', false);
        $oControl->expects($this->once())->method('_runOnce');
        $oControl->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->once())->method('_process')->with($this->equalTo("admin_start"), $this->equalTo("testFnc"));
        $oControl->start();
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStartSystemComponentExceptionHandled_NotDebugMode()
    {
        oxRegistry::get("OxConfigFile")->setVar('iDebug', 0);
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception("HandledOxSystemComponentException"); }');

        $oControl = $this->getMock("oxShopControl", array("_runOnce", "_process"), array(), '', false);
        $oControl->expects($this->any())->method('_process')->will($this->throwException(oxNew('oxSystemComponentException')));

        try {
            $oControl->start('classToLoad', 'functionToLoad');
        } catch (Exception $oExcp) {
            $this->assertEquals("HandledOxSystemComponentException", $oExcp->getMessage());

            return;
        }
        $this->fail("Error while executing testStartSystemComponentExceptionThrown()");
    }

    /**
     * Testing oxShopControl::start()
     * oxUtilsView::addErrorToDispla() should not be called in not debug mode
     *
     * @return null
     */
    public function testStartSystemComponentExceptionHandled_onlyInDebugMode()
    {
        oxRegistry::get("OxConfigFile")->setVar('iDebug', -1);
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ throw new Exception("HandledOxSystemComponentException"); }');
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception("oxAddErrorToDisplayException"); }');

        $oControl = $this->getMock("oxShopControl", array("_runOnce", "_process"), array(), '', false);
        $oControl->expects($this->any())->method('_process')->will($this->throwException(oxNew('oxSystemComponentException')));

        try {
            $oControl->start('classToLoad', 'functionToLoad');
        } catch (Exception $oExcp) {
            $this->assertEquals("HandledOxSystemComponentException", $oExcp->getMessage());

            return;
        }
        $this->fail("Error while executing testStartSystemComponentExceptionThrown()");
    }

    /**
     * Test unhandled exception with Debug ON
     *
     * @return null
     */
    public function testStartExceptionWithDebug()
    {
        $this->setExpectedException('oxException', 'log debug');

        $this->setRequestParameter('cl', 'testClass');
        $this->setRequestParameter('fnc', 'testFnc');

        $oUtilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'), array(), '', false);
        $oUtilsView->expects($this->any())->method('addErrorToDisplay');

        $oMockEx = $this->getMock('oxException', array('debugOut'));
        $oMockEx->expects($this->once())->method('debugOut')->will($this->throwException(new oxException('log debug')));

        $oControl = $this->getMock("oxShopControl", array("getConfig", "_runOnce", "isAdmin", "_process", "_isDebugMode"), array(), '', false, false, true);
        $oControl->expects($this->any())->method('getConfig');
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->any())->method('_process')->with($this->equalTo("testClass"), $this->equalTo("testFnc"))->will($this->throwException($oMockEx));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));

        $oControl->start();
    }

    /**
     * Test unhandled exception with debug OFF
     *
     * @return null
     */
    public function testStartExceptionNoDebug()
    {
        $this->setExpectedException('oxException', 'log debug');

        $this->setRequestParameter('cl', 'testClass');
        $this->setRequestParameter('fnc', 'testFnc');

        $oMockEx = $this->getMock('oxException', array('debugOut'));
        $oMockEx->expects($this->once())->method('debugOut')->will($this->throwException(new oxException('log debug')));

        $oControl = $this->getMock("oxShopControl", array("getConfig", "_runOnce", "_process", "_isDebugMode"), array(), '', false, false, true);
        $oControl->expects($this->any())->method('getConfig');
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->once())->method('_process')->with($this->equalTo("testClass"), $this->equalTo("testFnc"))->will($this->throwException($oMockEx));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(false));

        $oControl->start();
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStartCookieExceptionHandled()
    {
        $this->getSession()->setVariable('actshop', null);
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ throw new Exception("oxCookieException"); }');

        $oConfig = $this->getMock("oxStdClass", array("isMall", "getConfigParam", "getShopId", "getShopHomeUrl"));
        $oConfig->expects($this->any())->method('isMall')->will($this->returnValue(true));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(999));
        $oConfig->expects($this->any())->method('getShopHomeUrl');

        $oControl = $this->getMock("oxShopControl", array("getConfig", "_runOnce", "isAdmin", "_process", "_isDebugMode"), array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_process')->will($this->throwException(oxNew('oxSystemComponentException')));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));

        try {
            $oControl->start();
        } catch (Exception $oExcp) {
            $this->assertEquals("oxCookieException", $oExcp->getMessage(), "Error while executing testStartCookieExceptionThrown()");

            return;
        }
        $this->fail("Error while executing testStartCookieExceptionThrown()");
    }

    /**
     * Testing oxShopControl::start()
     * oxUtilsView::addErrorToDispla() should not be called in not debug mode
     *
     * @return null
     */
    public function testStartCookieExceptionHandled_onlyInDebugMode()
    {
        oxTestModules::addFunction('oxUtilsView', 'addErrorToDisplay', '{ throw new Exception("oxAddErrorToDisplayException"); }');
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception("oxRedirectException"); }');

        $oControl = $this->getMock("oxShopControl", array("_runOnce", "isAdmin", "_process", "_isDebugMode"), array(), '', false);
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_process')->will($this->throwException(oxNew('oxCookieException')));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(false));

        try {
            $oControl->start();
        } catch (Exception $oExcp) {
            $this->assertEquals("oxRedirectException", $oExcp->getMessage());

            return;
        }
        $this->fail("Error while executing testStartCookieExceptionThrown_onlyInDebugMode()");
    }

    /**
     * Testing oxShopControl::start()
     * oxUtilsView::addErrorToDisplay() should not be called in not debug mode
     *
     * @return null
     */
    public function testStartConnectionExceptionHandled()
    {
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{ throw new Exception("Exception"); }');

        $oControl = $this->getMock("oxShopControl", array("_runOnce", "isAdmin", "_process", "_isDebugMode"), array(), '', false);
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_process')->will($this->throwException(new oxConnectionException()));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));

        try {
            $oControl->start();
        } catch (Exception $oExcp) {
            $this->assertNotEquals("oxConnectionException", $oExcp->getMessage());

            return;
        }
        $this->fail("Error while executing testStartCookieExceptionThrown_onlyInDebugMode()");
    }

    /**
     * Testing oxShopControl::_render()
     *
     * @return null
     */
    public function testRenderTemplateNotFound()
    {
        $oView = $this->getMock("oxview", array('render'));
        $oView->expects($this->once())->method('render')->will($this->returnValue('wrongTpl'));

        $oOut = $this->getMock("oxOutput", array('process', 'addVersionTags'));
        $oOut->expects($this->once())->method('process');
        $oOut->expects($this->any())->method('addVersionTags')->will($this->returnValue(true));

        $oControl = $this->getMock("oxShopControl", array("isAdmin", '_getOutputManager', '_isDebugMode'), array(), '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));

        $oSmarty = $this->getMock("Smarty", array('fetch'));
        $oSmarty->expects($this->once())->method('fetch')->with($this->equalTo("message/exception.tpl"));

        $oUtilsView = $this->getMock("oxUtilsView", array('getSmarty'));
        $oUtilsView->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $oControl->UNITrender($oView);
        oxRegistry::get("oxUtilsView")->passAllErrorsToView($aViewData, $oControl->UNITgetErrors('oxubase'));
        $this->assertTrue($aViewData["Errors"]["default"][0] instanceof ExceptionToDisplay);
    }

    /**
     * Testing oxShopControl::_process()
     */
    public function testProcess()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }
        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{ return false; }');
        oxTestModules::addFunction('oxUtils', 'setHeader', '{}');

        $sTplPath = $this->getConfig()->getConfigParam('sShopDir') . "/Application/views/";
        $sTplPath .= $this->getConfig()->getConfigParam('sTheme') . "/tpl/page/checkout/basket.tpl";

        $oConfig = $this->getMock("oxConfig", array("getTemplatePath", "getConfigParam", "pageClose"));
        $map = [['blLogging', null, true]];
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValueMap($map));
        $oConfig->expects($this->any())->method('getTemplatePath')->will($this->returnValue($sTplPath));

        $aTasks = array("isAdmin", "_log", "_startMonitor", "getConfig", "_stopMonitor", '_getOutputManager', '_executeMaintenanceTasks');

        $oOut = $this->getMock("oxOutput", array('output', 'flushOutput', 'sendHeaders'));
        $oOut->expects($this->once())->method('output')->with($this->equalTo('content'));
        $oOut->expects($this->once())->method('flushOutput')->will($this->returnValue(null));
        $oOut->expects($this->once())->method('sendHeaders')->will($this->returnValue(null));

        $oSmarty = $this->getMock("Smarty", array('fetch'));
        $oSmarty->expects($this->once())->method('fetch')->with($this->equalTo("page/info/content.tpl"));

        $oUtilsView = $this->getMock("oxUtilsView", array('getSmarty'));
        $oUtilsView->expects($this->any())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $oControl = $this->getMock("oxShopControl", $aTasks, array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->atLeastOnce())->method('_executeMaintenanceTasks');

        $oControl->UNITprocess("content", null);
    }

    public function testProcessJson()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }
        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{ return false; }');
        oxTestModules::addFunction('oxUtils', 'setHeader', '{}');

        $this->setRequestParameter('renderPartial', 'asd');

        $sTplPath = $this->getConfig()->getConfigParam('sShopDir') . "/Application/views/";
        $sTplPath .= $this->getConfig()->getConfigParam('sTheme') . "/tpl/page/checkout/basket.tpl";

        $oConfig = $this->getMock("oxConfig", array("getTemplatePath", "getConfigParam", "pageClose"));
        $map = [['blLogging', null, true]];
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValueMap($map));
        $oConfig->expects($this->any())->method('getTemplatePath')->will($this->returnValue($sTplPath));

        $aTasks = array("isAdmin", "_log", "_startMonitor", "getConfig", "_stopMonitor", '_getOutputManager', '_getErrors', '_executeMaintenanceTasks');

        $oOut = $this->getMock("oxOutput", array('output', 'flushOutput', 'sendHeaders', 'setOutputFormat'));
        $oOut->expects($this->at(0))->method('setOutputFormat')->with($this->equalTo(oxOutput::OUTPUT_FORMAT_JSON));
        $oOut->expects($this->at(1))->method('sendHeaders')->will($this->returnValue(null));
        $oOut->expects($this->at(3))->method('output')->with($this->equalTo('content'), $this->anything());
        $oOut->expects($this->at(4))->method('flushOutput')->will($this->returnValue(null));

        $oSmarty = $this->getMock("Smarty", array('fetch'));
        $oSmarty->expects($this->once())->method('fetch')->with($this->equalTo("page/info/content.tpl"));

        $oUtilsView = $this->getMock("oxUtilsView", array('getSmarty'));
        $oUtilsView->expects($this->any())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $oControl = $this->getMock("oxShopControl", $aTasks, array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->any())->method('_getErrors')->will($this->returnValue(array()));
        $oControl->expects($this->atLeastOnce())->method('_executeMaintenanceTasks');

        $oControl->UNITprocess("content", null);
    }

    public function testProcessJsonWithErrors()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }
        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{ return false; }');
        oxTestModules::addFunction('oxUtils', 'setHeader', '{}');

        $this->setRequestParameter('renderPartial', 'asd');

        $sTplPath = $this->getConfig()->getConfigParam('sShopDir') . "/Application/views/";
        $sTplPath .= $this->getConfig()->getConfigParam('sTheme') . "/tpl/page/checkout/basket.tpl";

        $oConfig = $this->getMock("oxConfig", array("getTemplatePath", "getConfigParam", "pageClose"));
        $map = [['blLogging', null, true]];
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValueMap($map));
        $oConfig->expects($this->any())->method('getTemplatePath')->will($this->returnValue($sTplPath));

        $aTasks = array("isAdmin", "_log", "_startMonitor", "getConfig", "_stopMonitor", '_getOutputManager', '_getErrors', '_executeMaintenanceTasks');

        $oOut = $this->getMock("oxOutput", array('output', 'flushOutput', 'sendHeaders', 'setOutputFormat'));
        $oOut->expects($this->at(0))->method('setOutputFormat')->with($this->equalTo(oxOutput::OUTPUT_FORMAT_JSON));
        $oOut->expects($this->at(1))->method('output')->with(
            $this->equalTo('errors'), $this->equalTo(
                array(
                     'other' => array('test1', 'test3'),
                     'default' => array('test2', 'test4'),
                )
            )
        );
        $oOut->expects($this->at(2))->method('sendHeaders')->will($this->returnValue(null));
        $oOut->expects($this->at(3))->method('output')->with($this->equalTo('content'), $this->anything());
        $oOut->expects($this->at(4))->method('flushOutput')->will($this->returnValue(null));

        $oSmarty = $this->getMock("Smarty", array('fetch'));
        $oSmarty->expects($this->once())->method('fetch')->with($this->equalTo("page/info/content.tpl"));

        $oUtilsView = $this->getMock("oxUtilsView", array('getSmarty'));
        $oUtilsView->expects($this->any())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $oControl = $this->getMock("oxShopControl", $aTasks, array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->atLeastOnce())->method('_executeMaintenanceTasks');
        $aErrors = array();
        $oDE = oxNew('oxDisplayError');
        $oDE->setMessage('test1');
        $aErrors['other'][] = serialize($oDE);
        $oDE->setMessage('test2');
        $aErrors['default'][] = serialize($oDE);
        $oDE->setMessage('test3');
        $aErrors['other'][] = serialize($oDE);
        $oDE->setMessage('test4');
        $aErrors['default'][] = serialize($oDE);

        $oControl->expects($this->any())->method('_getErrors')->will($this->returnValue($aErrors));

        $oControl->UNITprocess("content", null);
    }

    /**
     * Testing oxShopControl::_startMonitor() & oxShopControl::_stopMonitor()
     *
     * @return null
     */
    public function testStartMonitorStopMonitor()
    {
        $this->getConfig()->setConfigParam("blUseContentCaching", true);

        $oOut = $this->getMock("oxOutput", array('output'));
        $oOut->expects($this->never())->method('output');

        $oControl = $this->getMock("oxShopControl", array("isAdmin", '_getOutputManager'), array(), '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->never())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->UNITstartMonitor();
        $oControl->UNITstopMonitor();

        $oOut = $this->getMock("oxOutput", array('output'));
        $oOut->expects($this->once())->method('output')->with($this->equalTo('debuginfo'));

        $oControl = $this->getMock("oxShopControl", array("isAdmin", '_getOutputManager', '_isDebugMode'), array(), '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->once())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));
        $oControl->UNITstartMonitor();
        $oControl->UNITstopMonitor();
    }

    /**
     * Testing if shop is debug mode
     */
    public function testIsDebugMode()
    {
        $oControl = $this->getProxyClass("oxShopControl");
        $oConfigFile = oxRegistry::get('oxConfigFile');

        $oConfigFile->iDebug = -1;
        $this->assertTrue($oControl->UNITisDebugMode());

        $oConfigFile->iDebug = 0;
        $this->assertFalse($oControl->UNITisDebugMode());
    }

    public function testGetErrors()
    {
        $this->setSessionParam('Errors', null);
        $oControl = oxNew('oxShopControl');
        $this->assertEquals(array(), $oControl->UNITgetErrors('start'));
        $this->assertEquals(array(), $this->getSessionParam('Errors'));
        $this->assertEquals(array(), $oControl->UNITgetErrors('start'));

        $this->setSessionParam('Errors', array());
        $oControl = oxNew('oxShopControl');
        $this->assertEquals(array(), $oControl->UNITgetErrors('start'));
        $this->assertEquals(array(), $this->getSessionParam('Errors'));
        $this->assertEquals(array(), $oControl->UNITgetErrors('start'));

        $this->setSessionParam('Errors', array('asd' => 'asd'));
        $oControl = oxNew('oxShopControl');
        $this->assertEquals(array('asd' => 'asd'), $oControl->UNITgetErrors('start'));
        $this->assertEquals(array(), $this->getSessionParam('Errors'));
        $this->assertEquals(array('asd' => 'asd'), $oControl->UNITgetErrors('start'));
    }

    public function testGetErrorsForActController()
    {
        $this->setSessionParam('Errors', array('asd' => 'asd'));
        $this->setSessionParam('ErrorController', array('asd' => 'start'));
        $oControl = oxNew('oxShopControl');
        $this->assertEquals(array('asd' => 'asd'), $oControl->UNITgetErrors('start'));
        $this->assertEquals(array(), $this->getSessionParam('Errors'));
        $this->assertEquals(array('asd' => 'asd'), $oControl->UNITgetErrors('start'));
        $this->assertEquals(array(), $this->getSessionParam('ErrorController'));
    }

    public function testGetErrorsForDifferentController()
    {
        $this->setSessionParam('Errors', array('asd' => 'asd'));
        $this->setSessionParam('ErrorController', array('asd' => 'oxwidget'));
        $oControl = oxNew('oxShopControl');
        $this->assertEquals(array('asd' => 'asd'), $oControl->UNITgetErrors('start'));
        $this->assertEquals(array('asd' => 'asd'), $this->getSessionParam('Errors'));
    }

    public function testGetOutputManager()
    {
        $oControl = oxNew('oxShopControl');
        $oOut = $oControl->UNITgetOutputManager();
        $this->assertTrue($oOut instanceof Output);
        $oOut1 = $oControl->UNITgetOutputManager();
        $this->assertSame($oOut, $oOut1);
    }

    /**
     * Test case for oxShopControl::_executeMaintenanceTasks();
     *
     * @return null
     */
    public function testExecuteMaintenanceTasks()
    {
        $oList = $this->getMock('oxArticleList', array('updateUpcomingPrices'));
        $oList->expects($this->once())->method('updateUpcomingPrices');

        oxTestModules::addModuleObject('oxarticlelist', $oList);

        $oControl = oxNew("oxShopControl");
        $oControl->UNITexecuteMaintenanceTasks();
    }



    /**
     * 0005568: Execution of any private/protected Methods in any Controller by external requests to the shop possible
     */
    public function testCannotAccessProtectedMethod()
    {
        $sCL = 'Account';
        $sFNC = '_getLoginTemplate';
        $oProtectedMethodException = new oxSystemComponentException('Non public method cannot be accessed');

        $oView = $this->getMock($sCL, array('executeFunction', 'getFncName'));
        $oView->expects($this->never())->method('executeFunction');
        $oView->expects($this->once())->method('getFncName')->will($this->returnValue($sFNC));

        $oControl = $this->getMock('oxShopControl', array('_initializeViewObject', '_handleSystemException'));
        $oControl->expects($this->once())->method('_initializeViewObject')->with($sCL, $sFNC, null, null)->will($this->returnValue($oView));
        $oControl->expects($this->once())->method('_handleSystemException')->with($oProtectedMethodException)->will($this->returnValue(true));

        $oControl->start($sCL, $sFNC);
    }
}
