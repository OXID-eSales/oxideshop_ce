<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use Exception;
use modDB;
use oxException;
use OxidEsales\EshopCommunity\Core\Exception\ConnectionException;
use OxidEsales\EshopCommunity\Core\Exception\ExceptionToDisplay;
use OxidEsales\EshopCommunity\Core\Output;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use oxOutput;
use oxRegistry;
use oxSystemComponentException;
use oxTestModules;

// Force autoloading of Smarty class, so that mocking would work correctly.
class_exists('Smarty');

class ShopControlTest extends \OxidTestCase
{

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStart()
    {
        $this->setRequestParameter('cl', null);
        $this->setRequestParameter('fnc', "testFnc");

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("isMall", "getConfigParam", "getShopHomeUrl"));
        $oConfig->expects($this->any())->method('isMall')->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getShopHomeUrl');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("getConfig", "_runOnce", "isAdmin", "_process"), array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->once())->method('_runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->once())->method('_process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\StartController::class), $this->equalTo("testFnc"));

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
        modDB::getInstance()->addClassFunction('getOne', function ($x) {
            return 2;
        });

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getShopHomeUrl"));
        //$oConfig->expects( $this->never() )->method( 'getShopId' )->will( $this->returnValue( 999 ) );
        $oConfig->expects($this->never())->method('getShopHomeUrl');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("getConfig", "_runOnce", "isAdmin", "_process"), array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->once())->method('_runOnce');
        $oControl->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->once())->method('_process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\Admin\LoginController::class), $this->equalTo("testFnc"));
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

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("_runOnce", "isAdmin", "_process"), array(), '', false);
        $oControl->expects($this->once())->method('_runOnce');
        $oControl->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->once())->method('_process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\Admin\AdminStart::class), $this->equalTo("testFnc"));
        $oControl->start();
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStartSystemComponentExceptionHandled_NotDebugMode()
    {
        oxRegistry::get("oxConfigFile")->setVar('iDebug', 0);

        $componentException = $this->getMock(oxSystemComponentException::class, ['debugOut']);
        $componentException->expects($this->atLeastOnce())->method('debugOut');

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array("redirect"));
        $oxUtils->expects($this->atLeastOnce())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array("addErrorToDisplay"));
        $oxUtilsView->expects($this->never())->method("addErrorToDisplay")->with($componentException);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("_runOnce", "_process"), array(), '', false);
        $oControl->expects($this->any())->method('_process')->will($this->throwException($componentException));

        $oControl->start('classToLoad', 'functionToLoad');
    }

    /**
     * Testing oxShopControl::start()
     * oxUtilsView::addErrorToDispla() should not be called in not debug mode
     *
     * @return null
     */
    public function testStartSystemComponentExceptionHandled_onlyInDebugMode()
    {
        oxRegistry::get("oxConfigFile")->setVar('iDebug', -1);

        $componentException = $this->getMock(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class, ['debugOut']);
        $componentException->expects($this->any())->method('debugOut');

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array("redirect"));
        $oxUtils->expects($this->never())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array("addErrorToDisplay"));
        $oxUtilsView->expects($this->atLeastOnce())->method("addErrorToDisplay")->with($componentException);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("_runOnce", "_process"), array(), '', false);
        $oControl->expects($this->any())->method('_process')->will($this->throwException($componentException));

        try {
            $oControl->start('basket');
        } catch (Exception $oExcp) {
            // To handle exception _process is called one more time in debug mode, that's why it's needed to be caught.
        }
    }

    /**
     * Test unhandled exception with Debug ON
     *
     * @return null
     */
    public function testStartExceptionWithDebug()
    {
        $this->expectException('oxException');
        $this->expectExceptionMessage('log debug');

        $this->setRequestParameter('cl', 'basket');

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('addErrorToDisplay'), array(), '', false);
        $oUtilsView->expects($this->any())->method('addErrorToDisplay');

        $oMockEx = $this->getMock(\OxidEsales\Eshop\Core\Exception\StandardException::class, array('debugOut'));
        $oMockEx->expects($this->once())->method('debugOut')->will($this->throwException(new oxException('log debug')));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("getConfig", "_runOnce", "isAdmin", "_process", "_isDebugMode", 'getStartControllerKey'), array(), '', false, false, true);
        $oControl->expects($this->any())->method('getConfig');
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->once())->method('getStartControllerKey')->will($this->returnValue('basket'));
        $oControl->expects($this->any())->method('_process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\BasketController::class))->will($this->throwException($oMockEx));
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
        $this->expectException('oxException');
        $this->expectExceptionMessage('log debug');

        $this->setRequestParameter('cl', 'testClassId');
        $this->setRequestParameter('fnc', 'testFnc');

        $oMockEx = $this->getMock(\OxidEsales\Eshop\Core\Exception\StandardException::class, array('debugOut'));
        $oMockEx->expects($this->once())->method('debugOut')->will($this->throwException(new oxException('log debug')));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("getConfig", "_runOnce", "_process", "_isDebugMode", 'getStartControllerKey'), array(), '', false, false, true);
        $oControl->expects($this->any())->method('getConfig');
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->once())->method('getStartControllerKey')->will($this->returnValue('testClass'));
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

        $componentException = $this->getMock(\OxidEsales\Eshop\Core\Exception\CookieException::class, ['debugOut']);
        $componentException->expects($this->any())->method('debugOut');

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array("redirect"));
        $oxUtils->expects($this->atLeastOnce())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array("addErrorToDisplay"));
        $oxUtilsView->expects($this->never())->method("addErrorToDisplay")->with($componentException);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oConfig = $this->getMock("StdClass", array("isMall", "getConfigParam", "getShopId", "getShopHomeUrl"));
        $oConfig->expects($this->any())->method('isMall')->will($this->returnValue(true));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(999));
        $oConfig->expects($this->any())->method('getShopHomeUrl');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("getConfig", "_runOnce", "isAdmin", "_process", "_isDebugMode"), array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_process')->will($this->throwException($componentException));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(false));

        $oControl->start();
    }

    /**
     * Testing oxShopControl::start()
     * oxUtilsView::addErrorToDispla() should not be called in not debug mode
     *
     * @return null
     */
    public function testStartCookieExceptionHandled_onlyInDebugMode()
    {
        $componentException = $this->getMock(\OxidEsales\Eshop\Core\Exception\CookieException::class, ['debugOut']);
        $componentException->expects($this->any())->method('debugOut');

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array("redirect"));
        $oxUtils->expects($this->atLeastOnce())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array("addErrorToDisplay"));
        $oxUtilsView->expects($this->atLeastOnce())->method("addErrorToDisplay")->with($componentException);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("_runOnce", "isAdmin", "_process", "_isDebugMode"), array(), '', false);
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_process')->will($this->throwException($componentException));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));

        $oControl->start();
    }

    /**
     * Testing oxShopControl::start()
     * oxUtilsView::addErrorToDisplay() should not be called in not debug mode
     */
    public function testStartConnectionExceptionHandled()
    {
        $exceptionMock = $this->getMock(ConnectionException::class, ['debugOut']);
        $exceptionMock->expects($this->any())->method('debugOut');

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, array("redirect"));
        $oxUtils->expects($this->never())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array("addErrorToDisplay"));
        $oxUtilsView->expects($this->atLeastOnce())->method("addErrorToDisplay")->with($exceptionMock);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("_runOnce", "isAdmin", "_process", "_isDebugMode"), array(), '', false);
        $oControl->expects($this->any())->method('_runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_process')->will($this->throwException($exceptionMock));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));

        try {
            $oControl->start();
        } catch (Exception $oExcp) {
            // To handle exception _process is called one more time in debug mode, that's why it's needed to be caught.
        }
    }

    /**
     * Testing oxShopControl::_render()
     * An Exception is caught and reported to the exception log.
     */
    public function testRenderTemplateNotFound()
    {
        ContainerFactory::resetContainer();
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, array('render'));
        $oView->expects($this->once())->method('render')->will($this->returnValue('wrongTpl'));

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, array('process', 'addVersionTags'));
        $oOut->expects($this->once())->method('process');
        $oOut->expects($this->any())->method('addVersionTags')->will($this->returnValue(true));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("isAdmin", '_getOutputManager', '_isDebugMode'), array(), '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));

        $oSmarty = $this->getMock("Smarty", array('fetch'));
        $oSmarty->expects($this->once())->method('fetch')
            ->with($this->equalTo("message/exception.tpl"))
            ->will($this->returnValue(''));

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('getSmarty'));
        $oUtilsView->expects($this->once())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxTestModules::addModuleObject("oxUtilsView", $oUtilsView);

        $oControl->UNITrender($oView);
        \OxidEsales\Eshop\Core\Registry::getUtilsView()->passAllErrorsToView($aViewData, $oControl->UNITgetErrors('oxubase'));
        $this->assertTrue($aViewData["Errors"]["default"][0] instanceof ExceptionToDisplay);

        /**
         * Although no exception is thrown, the underlying error will be logged in oxideshop.log
         */
        $expectedExceptionClass = \OxidEsales\Eshop\Core\Exception\SystemComponentException::class;
        $this->assertLoggedException($expectedExceptionClass);
    }

    /**
     * Testing oxShopControl::_process()
     */
    public function testProcess()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }
        ContainerFactory::resetContainer();
        $this->getConfig()->setConfigParam('sTheme', 'azure');

        $controllerClassName = 'content';

        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{ return false; }');
        oxTestModules::addFunction('oxUtils', 'setHeader', '{}');

        $sTplPath = $this->getConfig()->getConfigParam('sShopDir') . "/Application/views/";
        $sTplPath .= $this->getConfig()->getConfigParam('sTheme') . "/tpl/page/checkout/basket.tpl";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getTemplatePath", "getConfigParam", "pageClose"));
        $map = [['blLogging', null, true]];
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValueMap($map));
        $oConfig->expects($this->any())->method('getTemplatePath')->will($this->returnValue($sTplPath));

        $aTasks = array("isAdmin", "_log", "_startMonitor", "getConfig", "_stopMonitor", '_getOutputManager', '_executeMaintenanceTasks');

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, array('output', 'flushOutput', 'sendHeaders'));
        $oOut->expects($this->once())->method('output')->with($this->equalTo($controllerClassName));
        $oOut->expects($this->once())->method('flushOutput')->will($this->returnValue(null));
        $oOut->expects($this->once())->method('sendHeaders')->will($this->returnValue(null));

        $oSmarty = $this->getSmartyMock($this->getTemplateName($controllerClassName));

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('getSmarty'));
        $oUtilsView->expects($this->any())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxTestModules::addModuleObject("oxUtilsView", $oUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, $aTasks, array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->atLeastOnce())->method('_executeMaintenanceTasks');

        $oControl->UNITprocess($controllerClassName, null);
    }

    public function testProcessJson()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }
        ContainerFactory::resetContainer();
        $this->getConfig()->setConfigParam('sTheme', 'azure');

        $controllerClassName = 'content';

        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{ return false; }');
        oxTestModules::addFunction('oxUtils', 'setHeader', '{}');

        $this->setRequestParameter('renderPartial', 'asd');

        $sTplPath = $this->getConfig()->getConfigParam('sShopDir') . "/Application/views/";
        $sTplPath .= $this->getConfig()->getConfigParam('sTheme') . "/tpl/page/checkout/basket.tpl";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getTemplatePath", "getConfigParam", "pageClose"));
        $map = [['blLogging', null, true]];
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValueMap($map));
        $oConfig->expects($this->any())->method('getTemplatePath')->will($this->returnValue($sTplPath));

        $aTasks = array("isAdmin", "_log", "_startMonitor", "getConfig", "_stopMonitor", '_getOutputManager', '_getErrors', '_executeMaintenanceTasks');

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, array('output', 'flushOutput', 'sendHeaders', 'setOutputFormat'));
        $oOut->expects($this->at(0))->method('setOutputFormat')->with($this->equalTo(oxOutput::OUTPUT_FORMAT_JSON));
        $oOut->expects($this->at(1))->method('sendHeaders')->will($this->returnValue(null));
        $oOut->expects($this->at(3))->method('output')->with($this->equalTo($controllerClassName), $this->anything());
        $oOut->expects($this->at(4))->method('flushOutput')->will($this->returnValue(null));

        $oSmarty = $this->getSmartyMock($this->getTemplateName($controllerClassName));

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('getSmarty'));
        $oUtilsView->expects($this->any())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxTestModules::addModuleObject("oxUtilsView", $oUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, $aTasks, array(), '', false);
        $oControl->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->any())->method('_getErrors')->will($this->returnValue(array()));
        $oControl->expects($this->atLeastOnce())->method('_executeMaintenanceTasks');

        $oControl->UNITprocess($controllerClassName, null);
    }

    public function testProcessJsonWithErrors()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }
        ContainerFactory::resetContainer();
        $this->getConfig()->setConfigParam('sTheme', 'azure');

        $controllerClassName = 'content';

        oxTestModules::addFunction('oxUtils', 'isSearchEngine', '{ return false; }');
        oxTestModules::addFunction('oxUtils', 'setHeader', '{}');

        $this->setRequestParameter('renderPartial', 'asd');

        $sTplPath = $this->getConfig()->getConfigParam('sShopDir') . "/Application/views/";
        $sTplPath .= $this->getConfig()->getConfigParam('sTheme') . "/tpl/page/checkout/basket.tpl";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("getTemplatePath", "getConfigParam", "pageClose"));
        $map = [['blLogging', null, true]];
        $oConfig->expects($this->any())->method('getConfigParam')->will($this->returnValueMap($map));
        $oConfig->expects($this->any())->method('getTemplatePath')->will($this->returnValue($sTplPath));

        $aTasks = array("isAdmin", "_log", "_startMonitor", "getConfig", "_stopMonitor", '_getOutputManager', '_getErrors', '_executeMaintenanceTasks');

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, array('output', 'flushOutput', 'sendHeaders', 'setOutputFormat'));
        $oOut->expects($this->at(0))->method('setOutputFormat')->with($this->equalTo(oxOutput::OUTPUT_FORMAT_JSON));
        $oOut->expects($this->at(1))->method('output')->with(
            $this->equalTo('errors'),
            $this->equalTo(
                array(
                'other'   => array('test1', 'test3'),
                'default' => array('test2', 'test4'),
            )
        )
        );
        $oOut->expects($this->at(2))->method('sendHeaders')->will($this->returnValue(null));
        $oOut->expects($this->at(3))->method('output')->with($this->equalTo($controllerClassName), $this->anything());
        $oOut->expects($this->at(4))->method('flushOutput')->will($this->returnValue(null));

        $oSmarty = $this->getSmartyMock($this->getTemplateName($controllerClassName));

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('getSmarty'));
        $oUtilsView->expects($this->any())->method('getSmarty')->will($this->returnValue($oSmarty));
        oxTestModules::addModuleObject("oxUtilsView", $oUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, $aTasks, array(), '', false);
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

        $oControl->UNITprocess($controllerClassName, null);
    }

    /**
     * Testing oxShopControl::_startMonitor() & oxShopControl::_stopMonitor()
     *
     * @return null
     */
    public function testStartMonitorStopMonitor()
    {
        $this->getConfig()->setConfigParam("blUseContentCaching", true);

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, array('output'));
        $oOut->expects($this->never())->method('output');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("isAdmin", '_getOutputManager'), array(), '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->never())->method('_getOutputManager')->will($this->returnValue($oOut));
        $oControl->UNITstartMonitor();
        $oControl->UNITstopMonitor();

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, array('output'));
        $oOut->expects($this->once())->method('output')->with($this->equalTo('debuginfo'));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array("isAdmin", '_getOutputManager', '_isDebugMode'), array(), '', false);
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
        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, array('updateUpcomingPrices'));
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
        $sCL = \OxidEsales\Eshop\Application\Controller\AccountController::class;
        $sFNC = '_getLoginTemplate';
        $oProtectedMethodException = new oxSystemComponentException('Non public method cannot be accessed');

        $oView = $this->getMock($sCL, array('executeFunction', 'getFncName'));
        $oView->expects($this->never())->method('executeFunction');
        $oView->expects($this->once())->method('getFncName')->will($this->returnValue($sFNC));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array('_initializeViewObject', '_handleSystemException'));
        $oControl->expects($this->once())->method('_initializeViewObject')->with($sCL, $sFNC, null, null)->will($this->returnValue($oView));
        $oControl->expects($this->once())->method('_handleSystemException')->with($oProtectedMethodException)->will($this->returnValue(true));

        $oControl->start($sCL, $sFNC);
    }

    /**
     * Test case that requested controller id matches known class.
     *
     * @return null
     */
    public function testStartWithMatchedRequestControllerIdDebugModeOn()
    {
        $controllerId = 'order';

        $this->setRequestParameter('cl', $controllerId);
        $this->setRequestParameter('fnc', 'testFnc');

        $control = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array('_process', 'handleRoutingException', '_isDebugMode'), array(), '', false, false, true);
        $control->expects($this->once())->method('_process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\OrderController::class));
        $control->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));
        $control->expects($this->never())->method('handleRoutingException');

        $control->start();
    }

    /**
     * Test case that requested controller id does not match any known class.
     *
     * @return null
     */
    public function testStartWithUnmatchedRequestControllerIdDebugModeOn()
    {
        $controllerId = 'unmatchedControllerId';
        $routingException = new \OxidEsales\Eshop\Core\Exception\RoutingException($controllerId);

        $this->setRequestParameter('cl', $controllerId);
        $this->setRequestParameter('fnc', 'testFnc');

        $control = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, array('_process', 'handleRoutingException', '_isDebugMode'), array(), '', false, false, true);
        $control->expects($this->once())->method('_process')->with($this->equalTo($controllerId));
        $control->expects($this->any())->method('_isDebugMode')->will($this->returnValue(true));
        $control->expects($this->once())->method('handleRoutingException')->with($this->equalTo($routingException));

        $control->start();
    }

    protected function tearDown()
    {
        parent::tearDown();

        modDB::getInstance()->cleanup();
    }

    /**
     * Check that fetch method returns expected template name.
     * Could be useful as an integrational test to test that template from controller is set to Smarty
     *
     * @param $expectedTemplate
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function getSmartyMock($expectedTemplate)
    {
        $oSmarty = $this->getMock("Smarty", array('fetch'));
        $oSmarty->expects($this->once())->method('fetch')
            ->with($this->equalTo($expectedTemplate))
            ->will($this->returnValue('string'));

        return $oSmarty;
    }

    /**
     * Get name of active template for controller.
     * Run render() method as it might change the name.
     *
     * @param $controllerClassName
     *
     * @return string
     */
    private function getTemplateName($controllerClassName)
    {
        $control = oxNew($controllerClassName);
        $control->render();

        return $control->getTemplateName();
    }
}
