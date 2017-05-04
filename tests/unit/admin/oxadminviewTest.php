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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * Test adminView module.
 */
class testAdminView extends oxAdminView
{

    /**
     * Add posibility to cleanup static cache.
     *
     * @return null
     */
    public static function cleanup()
    {
        self::$_sAuthUserRights = null;
    }
}

/**
 * Testing oxAdminView class
 */
class Unit_Admin_oxAdminViewTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxuser');
        $myDB = oxDb::getDB();
        $myDB->execute("delete from oxseo where oxobjectid = '_testArt'");
        $myDB->execute("delete from oxnewssubscribed where oxuserid = '_testUser'");
        testAdminView::cleanup();
        modSession::getInstance()->cleanup();

        //resetting cached testing values
        $_GET["testReset"] = null;

        parent::tearDown();
    }

    /**
     * Test get service protocol.
     *
     * @return null
     */
    public function testGetServiceProtocol()
    {
        // SSL on
        $oConfig = $this->getMock("oxconfig", array("isSsl"));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(true));

        $oAdminView = $this->getMock("oxadminview", array("getConfig"), array(), '', false);
        $oAdminView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals("https", $oAdminView->UNITgetServiceProtocol());

        // SSL off
        $oConfig = $this->getMock("oxconfig", array("isSsl"));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(false));

        $oAdminView = $this->getMock("oxadminview", array("getConfig"), array(), '', false);
        $oAdminView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals("http", $oAdminView->UNITgetServiceProtocol());
    }

    /**
     * Test get service url.
     *
     * @return null
     */
    public function testGetServiceUrl()
    {


        $sPref = 'CE';

        // no lang abbr
        $this->getProxyClass("oxadminview");
        $oAdminView = $this->getMock("oxadminviewPROXY", array("_getServiceProtocol", "_getCountryByCode", "_getShopVersionNr"), array(), '', false);
        $oAdminView->expects($this->any())->method('_getServiceProtocol')->will($this->returnValue("testprotocol"));
        $oAdminView->expects($this->any())->method('_getCountryByCode')->will($this->returnValue("testcountrycode"));
        $oAdminView->expects($this->any())->method('_getShopVersionNr')->will($this->returnValue("testshopversion"));

        modSession::getInstance()->setVar('tpllanguage', 'de');

        $sTestUrl = "testprotocol://admin.oxid-esales.com/$sPref/testshopversion/testcountrycode/de/";
        $this->assertEquals($sTestUrl, $oAdminView->getServiceUrl());

        $oAdminView->setNonPublicVar('_sServiceUrl', null);
        $sTestUrl = "testprotocol://admin.oxid-esales.com/$sPref/testshopversion/testcountrycode/en/";
        $this->assertEquals($sTestUrl, $oAdminView->getServiceUrl('fr'));

        $oAdminView->setNonPublicVar('_sServiceUrl', null);
        $sTestUrl = "testprotocol://admin.oxid-esales.com/$sPref/testshopversion/testcountrycode/en/";
        $this->assertEquals($sTestUrl, $oAdminView->getServiceUrl("en"));
    }

    /**
     * Test get preview id.
     *
     * @return null
     */
    public function testGetPreviewId()
    {
        oxTestModules::addFunction('oxUtils', 'getPreviewId', '{ return "123"; }');
        $oAdminView = new oxadminview();
        $this->assertEquals("123", $oAdminView->getPreviewId());
    }

    /**
     * Test init.
     *
     * @return null
     */
    public function testInit()
    {
        $oAdminView = $this->getMock('oxadminview', array('_authorize'));
        $oAdminView->expects($this->once())->method('_authorize')->will($this->returnValue(true));
        $oAdminView->init();

        $this->assertEquals(oxRegistry::getSession()->getVariable('malladmin'), $oAdminView->getViewDataElement('malladmin'));
    }

    /**
     * Test setup navigation.
     *
     * @return null
     */
    public function testSetupNavigation()
    {
        $oNavigation = $this->getMock('oxnavigationtree', array('getListUrl', 'getEditUrl'));
        $oNavigation->expects($this->once())->method('getListUrl')->with($this->equalTo('xxx'))->will($this->returnValue('listurl'));
        $oNavigation->expects($this->once())->method('getEditUrl')->with($this->equalTo('xxx'))->will($this->returnValue('editurl'));

        $oAdminView = $this->getMock('oxadminview', array('getNavigation'));
        $oAdminView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));

        $oAdminView->UNITsetupNavigation('xxx');
        $this->assertEquals('listurl', $oAdminView->getViewDataElement('listurl'));
        $this->assertEquals('editurl', $oAdminView->getViewDataElement('editurl'));
    }

    /**
     * Test allow admin edit pe.
     *
     * @return null
     */
    public function testAllowAdminEditPE()
    {

        $oAdminView = new oxadminview();
        $this->assertTrue($oAdminView->UNITallowAdminEdit('xxx'));
    }





    /**
     * Test get view id.
     *
     * @return null
     */
    public function testGetViewId()
    {
        $oNavigation = $this->getMock('oxnavigationtree', array('getClassId'));
        $oNavigation->expects($this->once())->method('getClassId')->will($this->returnValue('xxx'));

        $oAdminView = $this->getMock('oxadminview', array('getNavigation'));
        $oAdminView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));

        $this->assertEquals('xxx', $oAdminView->getViewId());
    }

    /**
     * Test reset cached content .
     *
     * @return null
     */
    public function testResetContentCached()
    {
        $oAdminView = oxNew('oxAdminView');

        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ $_GET["testReset"] = "resetDone"; }');

        modConfig::getInstance()->setConfigParam("blClearCacheOnLogout", null);

        $oAdminView = oxNew('oxAdminView');
        $oAdminView->resetContentCache();

        $this->assertEquals('resetDone', $_GET["testReset"]);
    }

    /**
     * Checking reset when reset on logout is enabled and passing param
     *
     * @return null
     */
    public function testResetContentCachedWhenResetOnLogoutEnabled()
    {
        $oAdminView = oxNew('oxAdminView');

        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ $_GET["testReset"] = "resetDone"; }');

        modConfig::getInstance()->setConfigParam("blClearCacheOnLogout", 1);

        $oAdminView = oxNew('oxAdminView');
        $oAdminView->resetContentCache();

        $this->assertEquals(null, $_GET["testReset"]);
    }

    /**
     * Checking reset when reset on logout is enabled and passing param
     * to force reset
     *
     * @return null
     */
    public function testResetContentCachedWhenResetOnLogoutEnabledAndForceResetIsOn()
    {
        $oAdminView = oxNew('oxAdminView');

        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ $_GET["testReset"] = "resetDone"; }');

        modConfig::getInstance()->setConfigParam("blClearCacheOnLogout", 1);

        $oAdminView->resetContentCache(true);

        $this->assertEquals('resetDone', $_GET["testReset"]);
    }

    /**
     * Checking reseting counters cache
     *
     * @return null
     */
    public function testResetCounter()
    {
        modConfig::getInstance()->setConfigParam("blClearCacheOnLogout", null);
        oxTestModules::addFunction('oxUtilsCount', 'resetPriceCatArticleCount', '{ $_GET["testReset"]["priceCatCount"] = $aA[0]; }');
        oxTestModules::addFunction('oxUtilsCount', 'resetCatArticleCount', '{ $_GET["testReset"]["catCount"] = $aA[0]; }');
        oxTestModules::addFunction('oxUtilsCount', 'resetVendorArticleCount', '{ $_GET["testReset"]["vendorCount"] = $aA[0]; }');
        oxTestModules::addFunction('oxUtilsCount', 'resetManufacturerArticleCount', '{ $_GET["testReset"]["manufacturerCount"] = $aA[0]; }');

        $oAdminView = oxNew('oxAdminView');
        $oAdminView->resetCounter('priceCatArticle', 'testValue');
        $oAdminView->resetCounter('catArticle', 'testValue');
        $oAdminView->resetCounter('vendorArticle', 'testValue');
        $oAdminView->resetCounter('manufacturerArticle', 'testValue');

        $this->assertEquals('testValue', $_GET["testReset"]["priceCatCount"]);
        $this->assertEquals('testValue', $_GET["testReset"]["catCount"]);
        $this->assertEquals('testValue', $_GET["testReset"]["vendorCount"]);
        $this->assertEquals('testValue', $_GET["testReset"]["manufacturerCount"]);
    }

    /**
     * Checking reseting counters cache when reset on logout is enabled
     *
     * @return null
     */
    public function testResetCounterWhenResetOnLogoutEnabled()
    {
        modConfig::getInstance()->setConfigParam("blClearCacheOnLogout", 1);

        oxTestModules::addFunction('oxUtilsCount', 'resetPriceCatArticleCount', '{ $_GET["testReset"]["priceCatCount"] = $aA[0]; }');
        oxTestModules::addFunction('oxUtilsCount', 'resetCatArticleCount', '{ $_GET["testReset"]["catCount"] = $aA[0]; }');
        oxTestModules::addFunction('oxUtilsCount', 'resetVendorArticleCount', '{ $_GET["testReset"]["vendorCount"] = $aA[0]; }');
        oxTestModules::addFunction('oxUtilsCount', 'resetManufacturerArticleCount', '{ $_GET["testReset"]["manufacturerCount"] = $aA[0]; }');

        $oAdminView = oxNew('oxAdminView');
        $oAdminView->resetCounter('priceCatArticle', 'testValue');
        $oAdminView->resetCounter('catArticle', 'testValue');
        $oAdminView->resetCounter('vendorArticle', 'testValue');
        $oAdminView->resetCounter('manufacturerArticle', 'testValue');

        $this->assertEquals(null, $_GET["testReset"]["priceCatCount"]);
        $this->assertEquals(null, $_GET["testReset"]["catCount"]);
        $this->assertEquals(null, $_GET["testReset"]["vendorCount"]);
        $this->assertEquals(null, $_GET["testReset"]["manufacturerCount"]);
    }

    public function testAddGlobalParamsAddsSid()
    {
        $oUU = $this->getMock('oxUtilsUrl', array('processUrl'));
        $oUU->expects($this->any())->method('processUrl')->will($this->returnValue('sess:url'));
        //modInstances::addMod('oxUtilsUrl', $oUU);
        oxTestModules::addModuleObject('oxUtilsUrl', $oUU);

        $oAView = new oxAdminView();
        $oAView->addGlobalParams();
        $oViewCfg = $oAView->getViewConfig();

        $this->assertEquals('sess:url', $oViewCfg->getSelfLink());
        $this->assertEquals('sess:url', $oViewCfg->getAjaxLink());

    }

    public function testAuthorizeChecksSessionChallenge()
    {
        oxTestModules::addFunction('oxUtils', 'checkAccessRights', '{return true;}');
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{return array("asd");}');

        $oSess = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSess->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oAView = $this->getMock('oxAdminView', array('getSession'));
        $oAView->expects($this->once())->method('getSession')->will($this->returnValue($oSess));
        $this->assertEquals(true, $oAView->UNITauthorize());


        $oSess = $this->getMock('oxSession', array('checkSessionChallenge'));
        $oSess->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(false));
        $oAView = $this->getMock('oxAdminView', array('getSession'));
        $oAView->expects($this->once())->method('getSession')->will($this->returnValue($oSess));
        $this->assertEquals(false, $oAView->UNITauthorize());
    }


    /**
     * Tests oxAdminView::_getCountryByCode()
     *
     * @return null
     */
    public function testGetCountryByCode()
    {
        $oSubj = $this->getProxyClass("oxadminView");
        $sTestCode = "en";
        $this->assertEquals("international", $oSubj->UNITgetCountryByCode($sTestCode));
    }

    /**
     * Tests oxAdminView::_getCountryByCode()
     * when english language is deleted (bug #0001979)
     *
     * @return null
     */
    public function testGetCountryByCodeNoEng()
    {
        $oLang = $this->getMock('oxLang', array('getLanguageIds'));
        $oLang->expects($this->any())->method('getLanguageIds')->will($this->returnValue(array('de')));
        oxTestModules::addModuleObject('oxLang', $oLang);

        $oSubj = new oxadminView();
        $sTestCode = "de";
        $this->assertEquals("germany", $oSubj->UNITgetCountryByCode($sTestCode));
    }

    /**
     * Tests oxAdminView::_getCountryByCode(), when different active language is set. (#1707)
     *
     * @return null;
     */
    public function testGetCountryByCodeEnglishDefault()
    {
        //faking language array
        $aLangArray = array("0" => "en", "1" => "de");

        $oLangMock = $this->getMock("oxLang", array("getLanguageIds"));
        $oLangMock->expects($this->atLeastOnce())->method("getLanguageIds")->will($this->returnValue($aLangArray));
        oxTestModules::addModuleObject('oxLang', $oLangMock);

        $oSubj = $this->getProxyClass("oxadminView");
        $sTestCode = "de";

        //expecting same result due to faked language array
        $this->assertEquals("germany", $oSubj->UNITgetCountryByCode($sTestCode));
    }

    /**
     * test case for oxAdminView::getEditObjectId()/oxAdminView::setEditObjectId()
     */
    public function testSetEditObjectIdGetEditObjectId()
    {
        modConfig::setRequestParameter("oxid", null);
        modSession::getInstance()->setVar("saved_oxid", "testSessId");

        $oView = new oxAdminView();
        $this->assertEquals("testSessId", $oView->getEditObjectId());

        modConfig::setRequestParameter("oxid", "testRequestId");
        modSession::getInstance()->setVar("saved_oxid", "testSessId");

        $oView = new oxAdminView();
        $this->assertEquals("testRequestId", $oView->getEditObjectId());

        modConfig::setRequestParameter("oxid", "testRequestId");
        modSession::getInstance()->setVar("saved_oxid", "testSessId");

        $oView = new oxAdminView();
        $oView->setEditObjectId("testSetId");
        $this->assertEquals("testSetId", $oView->getEditObjectId());
    }
}
