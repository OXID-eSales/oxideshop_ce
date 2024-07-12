<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use Exception;
use modDB;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Exception\ConnectionException;
use OxidEsales\EshopCommunity\Core\Output;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use oxOutput;
use oxRegistry;
use oxTestModules;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

class ShopControlTest extends \OxidTestCase
{
    use ProphecyTrait;

    protected function tearDown(): void
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

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isMall", "getConfigParam", "getShopHomeUrl"]);
        $oConfig->expects($this->any())->method('isMall')->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getShopHomeUrl');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["getConfig", "runOnce", "isAdmin", "process"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oControl->expects($this->once())->method('runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->once())->method('process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\StartController::class), $this->equalTo("testFnc"));

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
        modDB::getInstance()->addClassFunction('getOne', fn($x) => 2);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopHomeUrl"]);
        //$oConfig->expects( $this->never() )->method( 'getShopId' )->will( $this->returnValue( 999 ) );
        $oConfig->expects($this->never())->method('getShopHomeUrl');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["getConfig", "runOnce", "isAdmin", "process"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oControl->expects($this->once())->method('runOnce');
        $oControl->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->once())->method('process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\Admin\LoginController::class), $this->equalTo("testFnc"));
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

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["runOnce", "isAdmin", "process"], [], '', false);
        $oControl->expects($this->once())->method('runOnce');
        $oControl->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->once())->method('process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\Admin\AdminStart::class), $this->equalTo("testFnc"));
        $oControl->start();
    }

    /**
     * Testing oxShopControl::start()
     * @dataProvider unknownControllerClass
     * @return null
     */
    public function testStartUnknownController_Redirect404($controllerName)
    {
        Registry::set(\OxidEsales\Eshop\Core\Routing\ControllerClassNameResolver::class, null); //reset

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())->method('error');

        Registry::set('logger', $logger);

        $utils = $this->getMockBuilder(\OxidEsales\Eshop\Core\Utils::class)->getMock();
        $utils
            ->expects($this->once())
            ->method('handlePageNotFoundError');

        Registry::set(\OxidEsales\Eshop\Core\Utils::class, $utils);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["runOnce", "handleSystemException"], [], '', false);

        $oControl->start($controllerName, 'functionToLoad');
    }

    public function unknownControllerClass()
    {
        return [
            'unknown controller class' => ['unknownClass'],
            'any oxid class not allowed' => [\OxidEsales\Eshop\Core\Utils::class],
        ];
    }

    /**
     * Testing oxShopControl::start()
     * oxUtilsView::addErrorToDispla() should not be called in not debug mode
     *
     * @return null
     */
    public function testStartSystemComponentExceptionHandled_onlyInDebugMode()
    {
        $logger = $this->getMock(LoggerInterface::class);
        Registry::set('logger', $logger);

        oxRegistry::get("oxConfigFile")->setVar('iDebug', -1);

        $componentException = $this->getMock(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class);

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ["redirect"]);
        $oxUtils->expects($this->never())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ["addErrorToDisplay"]);
        $oxUtilsView->expects($this->atLeastOnce())->method("addErrorToDisplay")->with($componentException);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["runOnce", "process"], [], '', false);
        $oControl->expects($this->any())->method('process')->will($this->throwException($componentException));

        try {
            $oControl->start('basket');
        } catch (Exception) {
            // To handle exception _process is called one more time in debug mode, that's why it's needed to be caught.
        }
    }

    /**
     * Testing oxShopControl::start()
     *
     * @return null
     */
    public function testStartCookieExceptionHandled()
    {
        $this->getSession()->setVariable('actshop', null);

        $componentException = $this->getMock(\OxidEsales\Eshop\Core\Exception\CookieException::class);

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ["redirect"]);
        $oxUtils->expects($this->atLeastOnce())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ["addErrorToDisplay"]);
        $oxUtilsView->expects($this->never())->method("addErrorToDisplay")->with($componentException);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oConfig = $this->getMock(Config::class, ["isMall", "getConfigParam", "getShopId", "getShopHomeUrl"]);
        $oConfig->expects($this->any())->method('isMall')->will($this->returnValue(true));
        $oConfig->expects($this->any())->method('getShopId')->will($this->returnValue(999));
        $oConfig->expects($this->any())->method('getShopHomeUrl');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["getConfig", "runOnce", "isAdmin", "process", "isDebugMode"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oControl->expects($this->any())->method('runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('process')->will($this->throwException($componentException));
        $oControl->expects($this->any())->method('isDebugMode')->will($this->returnValue(false));

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
        $componentException = $this->getMock(\OxidEsales\Eshop\Core\Exception\CookieException::class);

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ["redirect"]);
        $oxUtils->expects($this->atLeastOnce())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ["addErrorToDisplay"]);
        $oxUtilsView->expects($this->atLeastOnce())->method("addErrorToDisplay")->with($componentException);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["runOnce", "isAdmin", "process", "isDebugMode"], [], '', false);
        $oControl->expects($this->any())->method('runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('process')->will($this->throwException($componentException));
        $oControl->expects($this->any())->method('isDebugMode')->will($this->returnValue(true));

        $oControl->start();
    }

    /**
     * Testing oxShopControl::start()
     * oxUtilsView::addErrorToDisplay() should not be called in not debug mode
     */
    public function testStartConnectionExceptionHandled()
    {
        $logger = $this->getMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('error');

        Registry::set('logger', $logger);

        $exceptionMock = $this->getMock(ConnectionException::class);

        $oxUtils = $this->getMock(\OxidEsales\Eshop\Core\Utils::class, ["redirect"]);
        $oxUtils->expects($this->never())->method("redirect");
        oxTestModules::addModuleObject("oxUtils", $oxUtils);

        $oxUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ["addErrorToDisplay"]);
        $oxUtilsView->expects($this->atLeastOnce())->method("addErrorToDisplay")->with($exceptionMock);
        oxTestModules::addModuleObject("oxUtilsView", $oxUtilsView);

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["runOnce", "isAdmin", "process", "isDebugMode"], [], '', false);
        $oControl->expects($this->any())->method('runOnce');
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('process')->will($this->throwException($exceptionMock));
        $oControl->expects($this->any())->method('isDebugMode')->will($this->returnValue(true));

        try {
            $oControl->start();
        } catch (Exception) {
            // To handle exception _process is called one more time in debug mode, that's why it's needed to be caught.
        }
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

        $aTasks = ["isAdmin", "log", "startMonitor", "stopMonitoring", 'getOutputManager', 'executeMaintenanceTasks', 'formOutput'];

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, ['output', 'flushOutput', 'sendHeaders']);
        $oOut->expects($this->once())->method('output')->with($this->equalTo($controllerClassName));
        $oOut->expects($this->once())->method('flushOutput')->will($this->returnValue(null));
        $oOut->expects($this->once())->method('sendHeaders')->will($this->returnValue(null));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, $aTasks, [], '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->atLeastOnce())->method('executeMaintenanceTasks');
        $oControl->expects($this->any())->method('formOutput')->will($this->returnValue('string'));

        $oControl->process($controllerClassName, null);
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

        $aTasks = ["isAdmin", "log", "startMonitor", "stopMonitoring", 'getOutputManager', 'getErrors', 'executeMaintenanceTasks', 'formOutput'];

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, ['output', 'flushOutput', 'sendHeaders', 'setOutputFormat']);
        $oOut->method('setOutputFormat')->with($this->equalTo(oxOutput::OUTPUT_FORMAT_JSON));
        $oOut->method('sendHeaders')->will($this->returnValue(null));
        $oOut->method('flushOutput')->will($this->returnValue(null));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, $aTasks, [], '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->any())->method('getErrors')->will($this->returnValue([]));
        $oControl->expects($this->atLeastOnce())->method('executeMaintenanceTasks');
        $oControl->expects($this->any())->method('formOutput')->will($this->returnValue('string'));

        $oControl->process($controllerClassName, null);
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
        $sTplPath .= $this->getConfig()->getConfigParam('sTheme') . "/tpl/page/checkout/basket";

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getTemplatePath", "pageClose"]);
        $oConfig->expects($this->any())->method('getTemplatePath')->will($this->returnValue($sTplPath));

        $aTasks = ["isAdmin", "log", "startMonitor", "getConfig", "stopMonitoring", 'getOutputManager', 'getErrors', 'executeMaintenanceTasks', 'formOutput'];

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, ['output', 'flushOutput', 'sendHeaders', 'setOutputFormat']);
        $oOut->method('setOutputFormat')->with($this->equalTo(oxOutput::OUTPUT_FORMAT_JSON));
        $oOut
            ->method('output')
            ->withConsecutive(
                [
                    'errors',
                    ['other'   => ['test1', 'test3'], 'default' => ['test2', 'test4']]

                ],
                [$controllerClassName, $this->anything()]
            );

        $oOut->method('sendHeaders')->will($this->returnValue(null));
        $oOut->method('flushOutput')->will($this->returnValue(null));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, $aTasks, [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->any())->method('getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->atLeastOnce())->method('executeMaintenanceTasks');
        $oControl->expects($this->any())->method('formOutput')->will($this->returnValue('string'));
        $aErrors = [];
        $oDE = oxNew('oxDisplayError');
        $oDE->setMessage('test1');
        $aErrors['other'][] = serialize($oDE);
        $oDE->setMessage('test2');
        $aErrors['default'][] = serialize($oDE);
        $oDE->setMessage('test3');
        $aErrors['other'][] = serialize($oDE);
        $oDE->setMessage('test4');
        $aErrors['default'][] = serialize($oDE);

        $oControl->expects($this->any())->method('getErrors')->will($this->returnValue($aErrors));

        $oControl->process($controllerClassName, null);
    }

    /**
     * Testing oxShopControl::_startMonitor() & oxShopControl::stopMonitoring()
     *
     * @return null
     */
    public function testStartMonitorAndStopMonitoring()
    {
        $this->getConfig()->setConfigParam("blUseContentCaching", true);

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, ['output']);
        $oOut->expects($this->never())->method('output');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["isAdmin", 'getOutputManager'], [], '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oControl->expects($this->never())->method('getOutputManager')->will($this->returnValue($oOut));
        $oControl->startMonitor();
        $oControl->stopMonitoring();

        $oOut = $this->getMock(\OxidEsales\Eshop\Core\Output::class, ['output']);
        $oOut->expects($this->once())->method('output')->with($this->equalTo('debuginfo'));

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ["isAdmin", 'getOutputManager', 'isDebugMode'], [], '', false);
        $oControl->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oControl->expects($this->once())->method('getOutputManager')->will($this->returnValue($oOut));
        $oControl->expects($this->any())->method('isDebugMode')->will($this->returnValue(true));
        $oControl->startMonitor();
        $oControl->stopMonitoring();
    }

    /**
     * Testing if shop is debug mode
     */
    public function testIsDebugMode()
    {
        $oControl = $this->getProxyClass("oxShopControl");
        $oConfigFile = oxRegistry::get('oxConfigFile');

        $oConfigFile->iDebug = -1;
        $this->assertTrue($oControl->isDebugMode());

        $oConfigFile->iDebug = 0;
        $this->assertFalse($oControl->isDebugMode());
    }

    public function testGetErrors()
    {
        $this->setSessionParam('Errors', null);
        $oControl = oxNew('oxShopControl');
        $this->assertEquals([], $oControl->getErrors('start'));
        $this->assertEquals([], $this->getSessionParam('Errors'));
        $this->assertEquals([], $oControl->getErrors('start'));

        $this->setSessionParam('Errors', []);
        $oControl = oxNew('oxShopControl');
        $this->assertEquals([], $oControl->getErrors('start'));
        $this->assertEquals([], $this->getSessionParam('Errors'));
        $this->assertEquals([], $oControl->getErrors('start'));

        $this->setSessionParam('Errors', ['asd' => 'asd']);
        $oControl = oxNew('oxShopControl');
        $this->assertEquals(['asd' => 'asd'], $oControl->getErrors('start'));
        $this->assertEquals([], $this->getSessionParam('Errors'));
        $this->assertEquals(['asd' => 'asd'], $oControl->getErrors('start'));
    }

    public function testGetErrorsForActController()
    {
        $this->setSessionParam('Errors', ['asd' => 'asd']);
        $this->setSessionParam('ErrorController', ['asd' => 'start']);
        $oControl = oxNew('oxShopControl');
        $this->assertEquals(['asd' => 'asd'], $oControl->getErrors('start'));
        $this->assertEquals([], $this->getSessionParam('Errors'));
        $this->assertEquals(['asd' => 'asd'], $oControl->getErrors('start'));
        $this->assertEquals([], $this->getSessionParam('ErrorController'));
    }

    public function testGetErrorsForDifferentController()
    {
        $this->setSessionParam('Errors', ['asd' => 'asd']);
        $this->setSessionParam('ErrorController', ['asd' => 'oxwidget']);
        $oControl = oxNew('oxShopControl');
        $this->assertEquals(['asd' => 'asd'], $oControl->getErrors('start'));
        $this->assertEquals(['asd' => 'asd'], $this->getSessionParam('Errors'));
    }

    public function testGetOutputManager()
    {
        $oControl = oxNew('oxShopControl');
        $oOut = $oControl->getOutputManager();
        $this->assertTrue($oOut instanceof Output);
        $oOut1 = $oControl->getOutputManager();
        $this->assertSame($oOut, $oOut1);
    }

    /**
     * Test case for oxShopControl::_executeMaintenanceTasks();
     *
     * @return null
     */
    public function testExecuteMaintenanceTasks()
    {
        $oList = $this->getMock(\OxidEsales\Eshop\Application\Model\ArticleList::class, ['updateUpcomingPrices']);
        $oList->expects($this->once())->method('updateUpcomingPrices');

        oxTestModules::addModuleObject('oxarticlelist', $oList);

        $oControl = oxNew("oxShopControl");
        $oControl->executeMaintenanceTasks();
    }

    /**
     * 0005568: Execution of any private/protected Methods in any Controller by external requests to the shop possible
     */
    public function testCannotAccessProtectedMethod()
    {
        $sCL = \OxidEsales\Eshop\Application\Controller\AccountController::class;
        $sWebCL = 'account';
        $sFNC = 'getLoginTemplate';

        $oView = $this->getMock($sCL, ['executeFunction', 'getFncName', 'getClassKey']);
        $oView->expects($this->never())->method('executeFunction');
        $oView->expects($this->once())->method('getFncName')->will($this->returnValue($sFNC));

        Registry::set('logger', $this->getMockBuilder(LoggerInterface::class)->getMock());
        $className = $oView::class;
        Registry::getLogger()->expects($this->once())->method('error')
            ->with("Non public method cannot be accessed: {$className}::{$sFNC}");

        Registry::set(\OxidEsales\Eshop\Core\Utils::class, $this->getMockBuilder(\OxidEsales\Eshop\Core\Utils::class)->getMock());
        Registry::getUtils()->expects($this->once())->method('handlePageNotFoundError')->will($this->returnCallback(function (): never {
            throw new \Exception('404 Page will show');
        }));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('404 Page will show');

        $oControl = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ['initializeViewObject']);
        $oControl->expects($this->once())->method('initializeViewObject')->with($sCL, $sFNC, null, null)->will($this->returnValue($oView));

        $oControl->start($sWebCL, $sFNC);
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

        $control = $this->getMock(\OxidEsales\Eshop\Core\ShopControl::class, ['process', 'handleRoutingException', 'isDebugMode'], [], '', false, false, true);
        $control->expects($this->once())->method('process')->with($this->equalTo(\OxidEsales\Eshop\Application\Controller\OrderController::class));
        $control->expects($this->any())->method('isDebugMode')->will($this->returnValue(true));
        $control->expects($this->never())->method('handleRoutingException');

        $control->start();
    }
}
