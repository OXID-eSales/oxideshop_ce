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
namespace Unit\Application\Controller\Admin;

use \stdClass;
use \Exception;
use \oxRegistry;
use \oxTestModules;

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
        $this->assertEquals('nav_frame.tpl', $oView->render());
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
        $this->setRequestParameter("item", "home.tpl");
        $this->setRequestParameter("favorites", array(0, 1, 2));
        $this->setRequestParameter("navReload", false);
        $this->setRequestParameter("openHistory", true);

        $oDom = new stdClass();
        $oDom->documentElement = new stdClass();
        $oDom->documentElement->childNodes = 'testNodes';

        $oNavigation = $this->getMock("oxnavigationtree", array("getDomXml", "getListNodes"));
        $oNavigation->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $oNavigation->expects($this->any())->method('getListNodes')->will($this->returnValue("testNodes"));

        // testing..
        $oView = $this->getMock("Navigation", array("getNavigation", "_doStartUpChecks"));
        $oView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));
        $oView->expects($this->once())->method('_doStartUpChecks')->will($this->returnValue("check"));
        $this->assertEquals('home.tpl', $oView->render());

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
        $this->setRequestParameter("item", "home.tpl");
        $this->setRequestParameter("favorites", array(0, 1, 2));
        $this->setRequestParameter("navReload", true);
        $this->setRequestParameter("openHistory", true);
        $this->getSession()->setVariable("navReload", "true");

        $oDom = new stdClass();
        $oDom->documentElement = new stdClass();
        $oDom->documentElement->childNodes = 'testNodes';

        $oNavigation = $this->getMock("oxnavigationtree", array("getDomXml", "getListNodes"));
        $oNavigation->expects($this->once())->method('getDomXml')->will($this->returnValue($oDom));
        $oNavigation->expects($this->any())->method('getListNodes')->will($this->returnValue("testNodes"));

        // testing..
        $oView = $this->getMock("Navigation", array("getNavigation", "_doStartUpChecks"));
        $oView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));
        $oView->expects($this->never())->method('_doStartUpChecks')->will($this->returnValue("check"));
        $this->assertEquals('home.tpl', $oView->render());

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

        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo("blClearCacheOnLogout"))->will($this->returnValue(true));

        $oSession = $this->getMock("oxSession", array("destroy", "getId"));
        $oSession->expects($this->once())->method('destroy');
        $oSession->expects($this->never())->method('getId');

        // testing..
        $oView = $this->getMock("Navigation", array("getSession", "getConfig", "resetContentCache"), array(), '', false);
        $oView->expects($this->once())->method('getSession')->will($this->returnValue($oSession));
        $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
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
     * Navigation::Exturl() test case
     *
     * @return null
     */
    public function testExturlUrlDefinedByParam()
    {
        $sUrl = "http://admin.oxid-esales.com";

        // creating test file
        $rFile = fopen($this->getConfig()->getConfigParam('sCompileDir') . "/" . md5($sUrl) . '.html', "w+");
        fwrite($rFile, "</head>");
        fclose($rFile);

        oxTestModules::addFunction('oxUtils', 'getRemoteCachePath', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'redirect', '{ return true; }');
        oxTestModules::addFunction('oxUtils', 'showMessageAndExit', '{ throw new Exception($aA[0]); }');

        $this->setRequestParameter("url", $sUrl);

        $oConfig = $this->getMock("oxConfig", array("getConfigParam", "getVersion", "getFullEdition"));
        $oConfig->expects($this->at(0))->method('getConfigParam')->with($this->equalTo("blLoadDynContents"))->will($this->returnValue(true));
        $oConfig->expects($this->at(1))->method('getConfigParam')->with($this->equalTo("sCompileDir"))->will($this->returnValue($this->getConfig()->getConfigParam('sCompileDir')));
        $oConfig->expects($this->once())->method('getVersion')->will($this->returnValue("getVersion"));
        $oConfig->expects($this->once())->method('getFullEdition')->will($this->returnValue("getFullEdition"));

        try {
            // testing..
            $oView = $this->getMock("Navigation", array("getConfig"), array(), '', false);
            $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
            $oView->exturl();
        } catch (Exception $oExcp) {
            $sCurYear = date("Y");
            $this->assertEquals("<base href=\"http:/\"></head>\n  <!-- OXID eShop getFullEdition, Version getVersion, Shopping Cart System (c) OXID eSales AG 2003 - {$sCurYear} - http://www.oxid-esales.com -->", $oExcp->getMessage(), "Error in Navigation::exturl()");

            return;
        }
        $this->fail("Error in Navigation::exturl()");
    }

    /**
     * Navigation::Exturl() test case
     *
     * @return null
     */
    public function testExturlUrlDefinedByParamBlLoadDynContentsFalse()
    {
        oxTestModules::addFunction('oxUtils', 'redirect', '{ throw new Exception($aA[0]); }');
        $this->setRequestParameter("url", "testUrl");

        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->once())->method('getConfigParam')->with($this->equalTo("blLoadDynContents"))->will($this->returnValue(false));
        $oConfig->expects($this->never())->method('getVersion');
        $oConfig->expects($this->never())->method('getFullEdition');

        try {
            // testing..
            $oView = $this->getMock("Navigation", array("getConfig"), array(), '', false);
            $oView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
            $oView->exturl();
        } catch (Exception $oExcp) {
            $this->assertEquals("testUrl", $oExcp->getMessage(), "Error in Navigation::exturl()");

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
        $oView = $this->getMock("Navigation", array("_checkVersion"));
        $oView->expects($this->once())->method('_checkVersion')->will($this->returnValue("versionnotice"));
        $aState = $oView->UNITdoStartUpChecks();
        $this->assertTrue(is_array($aState));
        $this->assertTrue(isset($aState['message']));
        $this->assertTrue(isset($aState['warning']));
    }

    /**
     * Navigation::CheckVersion() test case
     *
     * @return null
     */
    public function testCheckVersion()
    {
        oxTestModules::addFunction('oxUtilsFile', 'readRemoteFileAsString', '{ return 4; }');
        oxTestModules::addFunction('oxLang', 'translateString', '{ return "Version %s is available."; }');

        $oConfig = $this->getMock("oxConfig", array("getVersion"));
        $oConfig->expects($this->once())->method('getVersion')->will($this->returnValue(3));

        // testing..
        $oView = $this->getMock("Navigation", array("getConfig"), array(), '', false);
        $oView->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals("Version 4 is available.", $oView->UNITcheckVersion());
    }
}
