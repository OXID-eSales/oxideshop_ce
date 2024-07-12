<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use Exception;
use OxidEsales\Eshop\Application\Controller\Admin\NavigationController;
use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ShopVersion;
use oxRegistry;
use oxTestModules;
use stdClass;

/**
 * Tests for Navigation class
 */
class NavigationTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Navigation::chshp() test case
     */
    public function testChshpPE()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $this->setRequestParameter("listview", "testlistview");
        $this->setRequestParameter("editview", "testeditview");
        $this->setRequestParameter("actedit", "testactedit");

        $oView = oxNew('Navigation');
        $oView->chshp();

        $this->assertSame("testlistview", $oView->getViewDataElement("listview"));
        $this->assertSame("testeditview", $oView->getViewDataElement("editview"));
        $this->assertSame("testactedit", $oView->getViewDataElement("actedit"));
        $this->assertEquals(true, $oView->getViewDataElement("loadbasefrm"));
    }

    /**
     * Navigation::Render() test case
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setOxCookie', '{}');
        $this->setRequestParameter("favorites", [0, 1, 2]);

        // testing..
        $oView = oxNew('Navigation');
        $this->assertSame('nav_frame', $oView->render());
    }

    /**
     * Navigation::Render() test case
     */
    public function testRenderPassingTemplateName()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setOxCookie', '{}');
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{return "a|b";}');

        $templateName = 'home.html.twig';
        $this->setRequestParameter("item", $templateName);
        $this->setRequestParameter("favorites", [0, 1, 2]);
        $this->setRequestParameter("navReload", false);
        $this->setRequestParameter("openHistory", true);

        $oDom = new stdClass();
        $oDom->documentElement = new stdClass();
        $oDom->documentElement->childNodes = 'testNodes';

        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml", "getListNodes"]);
        $oNavigation->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $oNavigation->method('getListNodes')->willReturn("testNodes");

        // testing..
        $oView = $this->getMock(NavigationController::class, ["getNavigation", "doStartUpChecks"]);
        $oView->expects($this->once())->method('getNavigation')->willReturn($oNavigation);
        $oView->expects($this->once())->method('doStartUpChecks')->willReturn("check");
        $this->assertSame($templateName, $oView->render());

        // checking vew data
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey("menustructure", $aViewData);
        $this->assertArrayHasKey("sVersion", $aViewData);
        $this->assertArrayHasKey("aMessage", $aViewData);
        $this->assertArrayHasKey("menufavorites", $aViewData);
        $this->assertArrayHasKey("aFavorites", $aViewData);
        $this->assertArrayHasKey("menuhistory", $aViewData);
        $this->assertArrayHasKey("blOpenHistory", $aViewData);
    }

    /**
     * Navigation::Render() test case
     */
    public function testRenderForceRequirementsCheckingNextTime()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setOxCookie', '{}');
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{return "a|b";}');
        $this->setRequestParameter("item", "home");
        $this->setRequestParameter("favorites", [0, 1, 2]);
        $this->setRequestParameter("navReload", true);
        $this->setRequestParameter("openHistory", true);
        $this->getSession()->setVariable("navReload", "true");

        $oDom = new stdClass();
        $oDom->documentElement = new stdClass();
        $oDom->documentElement->childNodes = 'testNodes';

        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ["getDomXml", "getListNodes"]);
        $oNavigation->expects($this->once())->method('getDomXml')->willReturn($oDom);
        $oNavigation->method('getListNodes')->willReturn("testNodes");

        // testing..
        $oView = $this->getMock(NavigationController::class, ["getNavigation", "doStartUpChecks"]);
        $oView->expects($this->once())->method('getNavigation')->willReturn($oNavigation);
        $oView->expects($this->never())->method('doStartUpChecks')->willReturn("check");
        $this->assertSame('home', $oView->render());

        // checking vew data
        $aViewData = $oView->getViewData();
        $this->assertArrayHasKey("menustructure", $aViewData);
        $this->assertArrayHasKey("sVersion", $aViewData);
        $this->assertArrayNotHasKey("aMessage", $aViewData);
        $this->assertArrayHasKey("menufavorites", $aViewData);
        $this->assertArrayHasKey("aFavorites", $aViewData);
        $this->assertArrayHasKey("menuhistory", $aViewData);
        $this->assertArrayHasKey("blOpenHistory", $aViewData);
        $this->assertNull(oxRegistry::getSession()->getVariable("navReload"));
    }

    /**
     * Navigation::Logout() test case
     */
    public function testLogout()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{}');

        $this->getSession()->setVariable('usr', "testUsr");
        $this->getSession()->setVariable('auth', "testAuth");
        $this->getSession()->setVariable('dynvalue', "testDynValue");
        $this->getSession()->setVariable('paymentid', "testPaymentId");

        Registry::getConfig()->setConfigParam('blClearCacheOnLogout', true);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ["destroy", "getId"]);
        $session->expects($this->once())->method('destroy');
        $session->expects($this->never())->method('getId');
        Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        // testing..
        $oView = $this->getMock(NavigationController::class, ["getConfig", "resetContentCache"], [], '', false);
        $oView->expects($this->once())->method('resetContentCache');
        $oView->logout();

        // testing if these were unset from session
        $this->assertNull(oxRegistry::getSession()->getVariable('usr'));
        $this->assertNull(oxRegistry::getSession()->getVariable('auth'));
        $this->assertNull(oxRegistry::getSession()->getVariable('dynvalue'));
        $this->assertNull(oxRegistry::getSession()->getVariable('paymentid'));
    }

    /**
     * Navigation::Exturl() test case
     */
    public function testExturl()
    {
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{ throw new Exception("showMessageAndExit"); }');
        $this->setRequestParameter("url", null);

        try {
            // testing..
            $oView = oxNew('Navigation');
            $oView->exturl();
        } catch (Exception $exception) {
            $this->assertSame("showMessageAndExit", $exception->getMessage(), "Error in Navigation::exturl()");

            return;
        }

        $this->fail("Error in Navigation::exturl()");
    }

    /**
     * Navigation::DoStartUpChecks() test case
     */
    public function testDoStartUpChecks()
    {
        $this->getConfig()->setConfigParam("blCheckForUpdates", true);

        // testing..
        $oView = $this->getMock(NavigationController::class, ["checkVersion"]);
        $oView->expects($this->once())->method('checkVersion')->willReturn("versionnotice");
        $aState = $oView->doStartUpChecks();
        $this->assertTrue(is_array($aState));
        $this->assertArrayHasKey('message', $aState);
        $this->assertArrayHasKey('warning', $aState);
    }

    public function testCheckVersion(): void
    {
        $latestVersion = '987';
        oxTestModules::addFunction('oxUtilsFile', 'readRemoteFileAsString', sprintf('{ return %s; }', $latestVersion));
        oxTestModules::addFunction('oxLang', 'translateString', '{ return "current ver.: %s new ver.: %s"; }');
        $controllerMock = new NavigationController();

        $actual =  $controllerMock->checkVersion();

        $this->assertStringContainsString(ShopVersion::getVersion(), $actual);
        $this->assertStringContainsString($latestVersion, $actual);
    }
}
