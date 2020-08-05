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
class NavigationTest extends \OxidTestCase
{

    /**
     * Navigation::chshp() test case
     *
     * @return null
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

        $this->assertEquals("testlistview", $oView->getViewDataElement("listview"));
        $this->assertEquals("testeditview", $oView->getViewDataElement("editview"));
        $this->assertEquals("testactedit", $oView->getViewDataElement("actedit"));
        $this->assertEquals(true, $oView->getViewDataElement("loadbasefrm"));
    }

    /**
     * Navigation::Render() test case
     *
     * @return null
     */
    public function testRender()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setOxCookie', '{}');
        $this->setRequestParameter("favorites", array(0, 1, 2));

        // testing..
        $oView = oxNew('Navigation');
        $this->assertEquals('nav_frame', $oView->render());
    }

    /**
     * Navigation::Render() test case
     *
     * @return null
     */
    public function testRenderPassingTemplateName()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setOxCookie', '{}');
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{return "a|b";}');
        $this->setRequestParameter("item", "home");
        $this->setRequestParameter("favorites", array(0, 1, 2));
        $this->setRequestParameter("navReload", false);
        $this->setRequestParameter("openHistory", true);

        $oDom = new stdClass();
        $oDom->documentElement = new stdClass();
        $oDom->documentElement->childNodes = 'testNodes';

        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, array("getDomXml", "getListNodes"));
        $oNavigation->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $oNavigation->expects($this->any())->method('getListNodes')->will($this->returnValue("testNodes"));

        // testing..
        $oView = $this->getMock(NavigationController::class, array("getNavigation", "doStartUpChecks"));
        $oView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));
        $oView->expects($this->once())->method('doStartUpChecks')->will($this->returnValue("check"));
        $this->assertEquals('home', $oView->render());

        // checking vew data
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData["menustructure"]));
        $this->assertTrue(isset($aViewData["sVersion"]));
        $this->assertTrue(isset($aViewData["aMessage"]));
        $this->assertTrue(isset($aViewData["menufavorites"]));
        $this->assertTrue(isset($aViewData["aFavorites"]));
        $this->assertTrue(isset($aViewData["menuhistory"]));
        $this->assertTrue(isset($aViewData["blOpenHistory"]));
    }

    /**
     * Navigation::Render() test case
     *
     * @return null
     */
    public function testRenderForceRequirementsCheckingNextTime()
    {
        oxTestModules::addFunction('oxUtilsServer', 'setOxCookie', '{}');
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{return "a|b";}');
        $this->setRequestParameter("item", "home");
        $this->setRequestParameter("favorites", array(0, 1, 2));
        $this->setRequestParameter("navReload", true);
        $this->setRequestParameter("openHistory", true);
        $this->getSession()->setVariable("navReload", "true");

        $oDom = new stdClass();
        $oDom->documentElement = new stdClass();
        $oDom->documentElement->childNodes = 'testNodes';

        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, array("getDomXml", "getListNodes"));
        $oNavigation->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $oNavigation->expects($this->any())->method('getListNodes')->will($this->returnValue("testNodes"));

        // testing..
        $oView = $this->getMock(NavigationController::class, array("getNavigation", "doStartUpChecks"));
        $oView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));
        $oView->expects($this->never())->method('doStartUpChecks')->will($this->returnValue("check"));
        $this->assertEquals('home', $oView->render());

        // checking vew data
        $aViewData = $oView->getViewData();
        $this->assertTrue(isset($aViewData["menustructure"]));
        $this->assertTrue(isset($aViewData["sVersion"]));
        $this->assertFalse(isset($aViewData["aMessage"]));
        $this->assertTrue(isset($aViewData["menufavorites"]));
        $this->assertTrue(isset($aViewData["aFavorites"]));
        $this->assertTrue(isset($aViewData["menuhistory"]));
        $this->assertTrue(isset($aViewData["blOpenHistory"]));
        $this->assertNull(oxRegistry::getSession()->getVariable("navReload"));
    }

    /**
     * Navigation::Logout() test case
     *
     * @return null
     */
    public function testLogout()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{}');

        $this->getSession()->setVariable('usr', "testUsr");
        $this->getSession()->setVariable('auth', "testAuth");
        $this->getSession()->setVariable('dynvalue', "testDynValue");
        $this->getSession()->setVariable('paymentid', "testPaymentId");

        Registry::getConfig()->setConfigParam('blClearCacheOnLogout', true);

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array("destroy", "getId"));
        $session->expects($this->once())->method('destroy');
        $session->expects($this->never())->method('getId');
        Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        // testing..
        $oView = $this->getMock(NavigationController::class, array("getConfig", "resetContentCache"), array(), '', false);
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
     *
     * @return null
     */
    public function testExturl()
    {
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{ throw new Exception("showMessageAndExit"); }');
        $this->setRequestParameter("url", null);

        try {
            // testing..
            $oView = oxNew('Navigation');
            $oView->exturl();
        } catch (Exception $oExcp) {
            $this->assertEquals("showMessageAndExit", $oExcp->getMessage(), "Error in Navigation::exturl()");

            return;
        }
        $this->fail("Error in Navigation::exturl()");
    }

    /**
     * Navigation::DoStartUpChecks() test case
     *
     * @return null
     */
    public function testDoStartUpChecks()
    {
        $this->getConfig()->setConfigParam("blCheckForUpdates", true);

        // testing..
        $oView = $this->getMock(NavigationController::class, array("checkVersion"));
        $oView->expects($this->once())->method('checkVersion')->will($this->returnValue("versionnotice"));
        $aState = $oView->doStartUpChecks();
        $this->assertTrue(is_array($aState));
        $this->assertTrue(isset($aState['message']));
        $this->assertTrue(isset($aState['warning']));
    }

    public function testCheckVersion(): void
    {
        $latestVersion = '987';
        oxTestModules::addFunction('oxUtilsFile', 'readRemoteFileAsString', "{ return $latestVersion; }");
        oxTestModules::addFunction('oxLang', 'translateString', '{ return "current ver.: %s new ver.: %s"; }');
        $controllerMock = new NavigationController();

        $actual =  $controllerMock->checkVersion();

        $this->assertStringContainsString(ShopVersion::getVersion(), $actual);
        $this->assertStringContainsString($latestVersion, $actual);
    }
}
