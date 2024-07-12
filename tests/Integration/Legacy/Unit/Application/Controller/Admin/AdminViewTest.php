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
class AdminViewTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
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
     */
    public function testGetServiceProtocol()
    {
        // SSL on
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isSsl"]);
        $oConfig->expects($this->once())->method('isSsl')->willReturn(true);

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertSame("https", $oAdminView->getServiceProtocol());

        // SSL off
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isSsl"]);
        $oConfig->expects($this->once())->method('isSsl')->willReturn(false);

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, ["getConfig"], [], '', false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertSame("http", $oAdminView->getServiceProtocol());
    }

    /**
     * Test get preview id.
     */
    public function testGetPreviewId()
    {
        oxTestModules::addFunction('oxUtils', 'getPreviewId', '{ return "123"; }');
        $oAdminView = oxNew('oxadminview');
        $this->assertSame("123", $oAdminView->getPreviewId());
    }

    /**
     * Test init.
     */
    public function testInit()
    {
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, ['authorize']);
        $oAdminView->expects($this->once())->method('authorize')->willReturn(true);
        $oAdminView->init();

        $this->assertEquals(oxRegistry::getSession()->getVariable('malladmin'), $oAdminView->getViewDataElement('malladmin'));
    }

    /**
     * Test setup navigation.
     */
    public function testSetupNavigation()
    {
        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ['getListUrl', 'getEditUrl']);
        $oNavigation->expects($this->once())->method('getListUrl')->with('xxx')->willReturn('listurl');
        $oNavigation->expects($this->once())->method('getEditUrl')->with('xxx')->willReturn('editurl');

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, ['getNavigation']);
        $oAdminView->expects($this->once())->method('getNavigation')->willReturn($oNavigation);

        $oAdminView->setupNavigation('xxx');
        $this->assertSame('listurl', $oAdminView->getViewDataElement('listurl'));
        $this->assertSame('editurl', $oAdminView->getViewDataElement('editurl'));
    }

    /**
     * Test allow admin edit pe.
     */
    public function testAllowAdminEditPE()
    {
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $oAdminView = oxNew('oxadminview');
        $this->assertTrue($oAdminView->allowAdminEdit('xxx'));
    }

    /**
     * Test get view id.
     */
    public function testGetViewIdMocked()
    {
        $oNavigation = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\NavigationTree::class, ['getClassId']);
        $oNavigation->expects($this->once())->method('getClassId')->willReturn('xxx');

        $oAdminView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class, ['getNavigation']);
        $oAdminView->expects($this->once())->method('getNavigation')->willReturn($oNavigation);

        $this->assertSame('xxx', $oAdminView->getViewId());
    }

    /**
     * Test get view id without mock.
     */
    public function testGetViewId()
    {
        $adminView = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ShopMain::class);
        $this->assertSame('tbclshop_main', $adminView->getViewId());
    }

    /**
     * Test get view id.
     * We simulate module chain extension case here.
     */
    public function testGetViewIdExtended()
    {
        //In module case we'd call oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ShopMain::class)
        // and get an instance of AdminViewTestShopMain::class.
        $adminView = oxNew(\OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin\AdminViewTestShopMain::class);
        $this->assertSame('tbclshop_main', $adminView->getViewId());
    }

    /**
     * Test get view id for class that should have no view id.
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
     */
    public function testResetContentCached()
    {
        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ $_GET["testReset"] = "resetDoneMain"; }');

        $oAdminView = oxNew('oxAdminView');
        $oAdminView->resetContentCache();

        $this->assertSame('resetDoneMain', $_GET["testReset"]);
    }

    /**
     * Checking reset when reset on logout is enabled and passing param
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
     */
    public function testResetContentCachedWhenResetOnLogoutEnabledAndForceResetIsOn()
    {
        oxTestModules::addFunction('oxUtils', 'oxResetFileCache', '{ $_GET["testReset"] = "resetDone"; }');

        $this->getConfig()->setConfigParam("blClearCacheOnLogout", 1);

        $oAdminView = oxNew('oxAdminView');
        $oAdminView->resetContentCache(true);

        $this->assertSame('resetDone', $_GET["testReset"]);
    }

    /**
     * Checking reseting counters cache
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

        $this->assertSame('testValue', $_GET["testReset"]["priceCatCount"]);
        $this->assertSame('testValue', $_GET["testReset"]["catCount"]);
        $this->assertSame('testValue', $_GET["testReset"]["vendorCount"]);
        $this->assertSame('testValue', $_GET["testReset"]["manufacturerCount"]);
    }

    /**
     * Checking reseting counters cache when reset on logout is enabled
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
        $oUU = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, ['processUrl']);
        $oUU->method('processUrl')->willReturn('sess:url');
        oxTestModules::addModuleObject('oxUtilsUrl', $oUU);

        $oAView = oxNew('oxAdminView');
        $oAView->addGlobalParams();

        $oViewCfg = $oAView->getViewConfig();

        $this->assertSame('sess:url', $oViewCfg->getSelfLink());
        $this->assertSame('sess:url', $oViewCfg->getAjaxLink());
    }

    public function testAuthorizeChecksSessionChallenge()
    {
        oxTestModules::addFunction('oxUtils', 'checkAccessRights', '{return true;}');
        oxTestModules::addFunction('oxUtilsServer', 'getOxCookie', '{return array("asd");}');

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session->expects($this->once())->method('checkSessionChallenge')->willReturn(true);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);
        $oAView = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class);
        $this->assertEquals(true, $oAView->authorize());

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['checkSessionChallenge']);
        $session->expects($this->once())->method('checkSessionChallenge')->willReturn(false);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);
        $oAView = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\AdminController::class);
        $this->assertEquals(false, $oAView->authorize());
    }


    /**
     * Tests oxAdminView::_getCountryByCode()
     */
    public function testGetCountryByCode()
    {
        $oSubj = $this->getProxyClass("oxadminView");
        $sTestCode = "en";
        $this->assertSame("international", $oSubj->getCountryByCode($sTestCode));
    }

    /**
     * Tests oxAdminView::_getCountryByCode()
     * when english language is deleted (bug #0001979)
     */
    public function testGetCountryByCodeNoEng()
    {
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ['getLanguageIds']);
        $oLang->method('getLanguageIds')->willReturn(['de']);
        oxTestModules::addModuleObject('oxLang', $oLang);

        $oSubj = oxNew('oxadminView');
        $sTestCode = "de";
        $this->assertSame("germany", $oSubj->getCountryByCode($sTestCode));
    }

    /**
     * Tests oxAdminView::_getCountryByCode(), when different active language is set. (#1707)
     *
     * @return null;
     */
    public function testGetCountryByCodeEnglishDefault()
    {
        //faking language array
        $aLangArray = ["0" => "en", "1" => "de"];

        $oLangMock = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ["getLanguageIds"]);
        $oLangMock->expects($this->atLeastOnce())->method("getLanguageIds")->willReturn($aLangArray);
        oxTestModules::addModuleObject('oxLang', $oLangMock);

        $oSubj = $this->getProxyClass("oxadminView");
        $sTestCode = "de";

        //expecting same result due to faked language array
        $this->assertSame("germany", $oSubj->getCountryByCode($sTestCode));
    }

    /**
     * test case for oxAdminView::getEditObjectId()/oxAdminView::setEditObjectId()
     */
    public function testSetEditObjectIdGetEditObjectId()
    {
        $this->setRequestParameter("oxid", null);
        $this->getSession()->setVariable("saved_oxid", "testSessId");

        $oView = oxNew('oxAdminView');
        $this->assertSame("testSessId", $oView->getEditObjectId());

        $this->setRequestParameter("oxid", "testRequestId");
        $this->getSession()->setVariable("saved_oxid", "testSessId");

        $oView = oxNew('oxAdminView');
        $this->assertSame("testRequestId", $oView->getEditObjectId());

        $this->setRequestParameter("oxid", "testRequestId");
        $this->getSession()->setVariable("saved_oxid", "testSessId");

        $oView = oxNew('oxAdminView');
        $oView->setEditObjectId("testSetId");
        $this->assertSame("testSetId", $oView->getEditObjectId());
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
