<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use oxArticleHelper;
use \oxDb;
use OxidEsales\Eshop\Application\Controller\Admin\AdminController;
use \oxRegistry;
use \oxTestModules;

/**
 * Testing oxAdminView class
 */
class AdminViewTest extends \OxidTestCase
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
        oxArticleHelper::cleanup();

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
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("isSsl"));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(true));

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, array("getConfig"), array(), '', false);
        $oAdminView->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals("https", $oAdminView->UNITgetServiceProtocol());

        // SSL off
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, array("isSsl"));
        $oConfig->expects($this->once())->method('isSsl')->will($this->returnValue(false));

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, array("getConfig"), array(), '', false);
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
        $sPref = $this->getConfig()->getEdition();

        // no lang abbr
        $this->getProxyClass(AdminController::class);
        $oAdminView = $this->getMock("OxidEsales_Eshop_Application_Controller_Admin_AdminControllerProxy", array("_getServiceProtocol", "_getCountryByCode", "_getShopVersionNr"), array(), '', false);
        $oAdminView->expects($this->any())->method('_getServiceProtocol')->will($this->returnValue("testprotocol"));
        $oAdminView->expects($this->any())->method('_getShopVersionNr')->will($this->returnValue("testshopversion"));

        $this->getSession()->setVariable('tpllanguage', 'de');

        $sTestUrl = "testprotocol://admin.oxid-esales.com/$sPref/testshopversion/international/de/";
        $this->assertEquals($sTestUrl, $oAdminView->getServiceUrl());

        $oAdminView->setNonPublicVar('_sServiceUrl', null);
        $sTestUrl = "testprotocol://admin.oxid-esales.com/$sPref/testshopversion/international/en/";
        $this->assertEquals($sTestUrl, $oAdminView->getServiceUrl('fr'));

        $oAdminView->setNonPublicVar('_sServiceUrl', null);
        $sTestUrl = "testprotocol://admin.oxid-esales.com/$sPref/testshopversion/international/en/";
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
        $oAdminView = oxNew('oxadminview');
        $this->assertEquals("123", $oAdminView->getPreviewId());
    }

    /**
     * Test init.
     *
     * @return null
     */
    public function testInit()
    {
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, array('_authorize'));
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
        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, array('getListUrl', 'getEditUrl'));
        $oNavigation->expects($this->once())->method('getListUrl')->with($this->equalTo('xxx'))->will($this->returnValue('listurl'));
        $oNavigation->expects($this->once())->method('getEditUrl')->with($this->equalTo('xxx'))->will($this->returnValue('editurl'));

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, array('getNavigation'));
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
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oAdminView = oxNew('oxadminview');
        $this->assertTrue($oAdminView->UNITallowAdminEdit('xxx'));
    }

    /**
     * Test get view id.
     *
     * @return null
     */
    public function testGetViewIdMocked()
    {
        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, array('getClassId'));
        $oNavigation->expects($this->once())->method('getClassId')->will($this->returnValue('xxx'));

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, array('getNavigation'));
        $oAdminView->expects($this->once())->method('getNavigation')->will($this->returnValue($oNavigation));

        $this->assertEquals('xxx', $oAdminView->getViewId());
    }

    /**
     * Test get view id without mock.
     *
     * @return null
     */
    public function testGetViewId()
    {
        $adminView = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ShopMain::class);
        $this->assertEquals('tbclshop_main', $adminView->getViewId());
    }

    /**
     * Test get view id.
     * We simulate module chain extension case here.
     *
     * @return null
     */
    public function testGetViewIdExtended()
    {
        //In module case we'd call oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ShopMain::class)
        // and get an instance of AdminViewTestShopMain::class.
        $adminView = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\AdminViewTestShopMain::class);
        $this->assertEquals('tbclshop_main', $adminView->getViewId());
    }

    /**
     * Test get view id for class that should have no view id.
     *
     * @return null
     */
    public function testGetViewIdNoneExists()
    {
        //In module case we'd call oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ShopMain::class)
        // and get an instance of AdminViewTestShopMain::class.
        $adminView = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\AdminViewTestSomeClass::class);
        $this->assertNull($adminView->getViewId());
    }

    /**
     * Test reset cached content .
     *
     * @return null
     */
    public function testResetContentCached()
    {
        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ $_GET["testReset"] = "resetDoneMain"; }');

        $oAdminView = oxNew('oxAdminView');
        $oAdminView->resetContentCache();

        $this->assertEquals('resetDoneMain', $_GET["testReset"]);
    }

    /**
     * Checking reset when reset on logout is enabled and passing param
     *
     * @return null
     */
    public function testResetContentCachedWhenResetOnLogoutEnabled()
    {
        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ $_GET["testReset"] = "resetDone"; }');

        $this->getConfig()->setConfigParam("blClearCacheOnLogout", 1);

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
        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ $_GET["testReset"] = "resetDone"; }');

        $this->getConfig()->setConfigParam("blClearCacheOnLogout", 1);

        $oAdminView = oxNew('oxAdminView');
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
        $this->getConfig()->setConfigParam("blClearCacheOnLogout", null);
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
        $this->getConfig()->setConfigParam("blClearCacheOnLogout", 1);

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
        $oUU = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, array('processUrl'));
        $oUU->expects($this->any())->method('processUrl')->will($this->returnValue('sess:url'));
        oxTestModules::addModuleObject('oxUtilsUrl', $oUU);

        $oAView = oxNew('oxAdminView');
        $oAView->addGlobalParams();
        $oViewCfg = $oAView->getViewConfig();

        $this->assertEquals('sess:url', $oViewCfg->getSelfLink());
        $this->assertEquals('sess:url', $oViewCfg->getAjaxLink());
    }

    public function testAuthorizeChecksSessionChallenge()
    {
        oxTestModules::addFunction('oxUtils', 'checkAccessRights', '{return true;}');
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{return array("asd");}');

        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSess->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(true));
        $oAView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, array('getSession'));
        $oAView->expects($this->once())->method('getSession')->will($this->returnValue($oSess));
        $this->assertEquals(true, $oAView->UNITauthorize());


        $oSess = $this->getMock(\OxidEsales\Eshop\Core\Session::class, array('checkSessionChallenge'));
        $oSess->expects($this->once())->method('checkSessionChallenge')->will($this->returnValue(false));
        $oAView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, array('getSession'));
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
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array('getLanguageIds'));
        $oLang->expects($this->any())->method('getLanguageIds')->will($this->returnValue(array('de')));
        oxTestModules::addModuleObject('oxLang', $oLang);

        $oSubj = oxNew('oxadminView');
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

        $oLangMock = $this->getMock(\OxidEsales\Eshop\Core\Language::class, array("getLanguageIds"));
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
        $this->setRequestParameter("oxid", null);
        $this->getSession()->setVariable("saved_oxid", "testSessId");

        $oView = oxNew('oxAdminView');
        $this->assertEquals("testSessId", $oView->getEditObjectId());

        $this->setRequestParameter("oxid", "testRequestId");
        $this->getSession()->setVariable("saved_oxid", "testSessId");

        $oView = oxNew('oxAdminView');
        $this->assertEquals("testRequestId", $oView->getEditObjectId());

        $this->setRequestParameter("oxid", "testRequestId");
        $this->getSession()->setVariable("saved_oxid", "testSessId");

        $oView = oxNew('oxAdminView');
        $oView->setEditObjectId("testSetId");
        $this->assertEquals("testSetId", $oView->getEditObjectId());
    }
}

/**
 * Class testClass
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin
 */
class AdminViewTestShopMain extends \OxidEsales\Eshop\Application\Controller\Admin\ShopMain
{
}

/**
 * Class AdminViewTestSomeClass
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin
 */
class AdminViewTestSomeClass extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
}
