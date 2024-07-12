<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\ViewConfig;
use OxidEsales\EshopCommunity\Application\Model\CountryList;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModuleAssetsPathResolverBridgeInterface;
use OxidEsales\Facts\Facts;
use oxTestModules;
use stdClass;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class ViewConfigTest extends \OxidTestCase
{
    protected function setUp(): void
    {
        $this->addTestModuleAssets();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        $this->removeTestModuleAssets();
        parent::tearDown();
    }

    /**
     * oxViewConfig::getHelpPageLink() test case
     */
    public function testGetHelpPageLink(): void
    {
        $sShopUrl = $this->getConfig()->getConfigParam("sShopURL");

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getActiveClassName"]);
        $oViewConfig->expects(self::once())->method("getActiveClassName")->willReturn("start");
        $this->assertEquals($sShopUrl . "Hilfe-Die-Startseite/", $oViewConfig->getHelpPageLink());

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getActiveClassName"]);
        $oViewConfig->expects(self::once())->method("getActiveClassName")->willReturn("alist");
        $this->assertEquals($sShopUrl . "Hilfe-Die-Produktliste/", $oViewConfig->getHelpPageLink());

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getActiveClassName"]);
        $oViewConfig->expects(self::once())->method("getActiveClassName")->willReturn("details");
        $this->assertEquals($sShopUrl . "Hilfe-Main/", $oViewConfig->getHelpPageLink());
    }

    /**
     * Check what happens when no help CMS content is found
     */
    public function testGetHelpPageLinkInactiveContents()
    {
        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ['getHelpContentIdents']);
        $oViewConfig->expects($this->once())->method("getHelpContentIdents")->will($this->returnValue(["none"]));
        $this->assertEquals("", $oViewConfig->getHelpPageLink());
    }

    /**
     * Check if correct help link is retrieved by default in english language
     */
    public function testGetHelpPageLinkActiveContents_EN()
    {
        $oViewConfig = oxNew('oxViewConfig');
        $this->getConfig()->setConfigParam("sDefaultLang", 1);
        $this->assertEquals($this->getConfig()->getShopUrl() . 'en/Help-Main/', $oViewConfig->getHelpPageLink());
    }

    public function testGetHomeLinkEng()
    {
        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        oxTestModules::addFunction("oxLang", "getBaseLanguage", "{return 1;}");

        $oViewConfig = oxNew('oxviewconfig');
        $this->assertEquals($this->getConfig()->getShopUrl() . 'en/home/', $oViewConfig->getHomeLink());
    }

    /**
     * Data provider for test case testGetHomeLink
     *
     * @return array
     */
    public function getHomeLinkDataProvider()
    {
        $sShopUrl = $this->getConfig()->getShopUrl();

        $iLangDE = 0;
        $iLangEN = 1;

        // Parameters:
        // - default shop language
        // - default browser language
        // - expected URL
        return [[$iLangDE, $iLangDE, $sShopUrl], [$iLangDE, $iLangEN, $sShopUrl . sprintf('index.php?lang=%d&amp;', $iLangDE)], [$iLangEN, $iLangDE, $sShopUrl . "index.php?lang=1&amp;"], [$iLangEN, $iLangEN, $sShopUrl]];
    }

    /**
     * Test case for getting eShop home link in different default languages and browser default languages
     *
     * @param int    $iDefaultShopLanguage    default shop language
     * @param int    $iDefaultBrowserLanguage default browser language
     * @param string $sExpectedUrl            expected URL
     *
     * @dataProvider getHomeLinkDataProvider
     */
    public function testGetHomeLink($iDefaultShopLanguage, $iDefaultBrowserLanguage, $sExpectedUrl)
    {
        /** @var $oLang oxLang | PHPUnit\Framework\MockObject\MockObject */
        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ['detectLanguageByBrowser']);
        $oLang
            ->expects($this->any())
            ->method('detectLanguageByBrowser')
            ->will($this->returnValue($iDefaultBrowserLanguage));

        Registry::set(\OxidEsales\Eshop\Core\Language::class, $oLang);

        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $this->setLanguage($iDefaultShopLanguage);
        $this->setConfigParam('sDefaultLang', $iDefaultShopLanguage);

        $oViewConfig = oxNew('oxViewConfig');
        $this->assertEquals(
            $sExpectedUrl,
            $oViewConfig->getHomeLink(),
            "URL is correct
            when default shop language is {$iDefaultShopLanguage}
            and default browser language is {$iDefaultBrowserLanguage}"
        );
    }

    public function testGetHomeLinkPe()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') :
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        endif;

        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");
        $oViewConfig = oxNew('oxviewconfig');
        $this->assertEquals($this->getConfig()->getShopURL(), $oViewConfig->getHomeLink());
    }

    /**
     * check config params getter
     */
    public function testGetShowWishlist()
    {
        Registry::getConfig()->setConfigParam('bl_showWishlist', 'lalala');
        $viewConfig = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $this->assertEquals('lalala', $viewConfig->getShowWishlist());
    }

    /**
     * check config params getter
     */
    public function testGetShowCompareList()
    {
        $oView = $this->createMock(\OxidEsales\Eshop\Core\Controller\BaseController::class);

        $oCfg = $this->createMock(\OxidEsales\Eshop\Core\Config::class);
        $oCfg->expects($this->any())
            ->method('getConfigParam')
            ->will($this->returnValueMap([
                ['bl_showCompareList', true],
                ['blDisableNavBars', true]
            ]));
        $oCfg->expects($this->any())->method('getActiveView')->will($this->returnValue($oView));

        $oVC = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $this->assertFalse($oVC->getShowCompareList());
    }

    /**
     * check config params getter
     */
    public function testGetShowListmania()
    {
        Registry::getConfig()->setConfigParam('bl_showListmania', 'lalala');
        $viewConfig = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $this->assertEquals('lalala', $viewConfig->getShowListmania());
    }

    /**
     * check config params getter
     */
    public function testGetShowVouchers()
    {
        Registry::getConfig()->setConfigParam('bl_showVouchers', 'lalala');
        $viewConfig = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $this->assertEquals('lalala', $viewConfig->getShowVouchers());
    }

    /**
     * check config params getter
     */
    public function testGetShowGiftWrapping()
    {
        Registry::getConfig()->setConfigParam('bl_showGiftWrapping', 'lalala');
        $viewConfig = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $this->assertEquals('lalala', $viewConfig->getShowGiftWrapping());
    }

    public function testGetRemoteAccessToken()
    {
        $oSubj = oxNew('oxViewConfig');
        $sTestToken1 = $oSubj->getRemoteAccessToken();
        $sTestToken2 = $oSubj->getRemoteAccessToken();

        $this->assertEquals($sTestToken1, $sTestToken2);
        $this->assertEquals(8, strlen((string) $sTestToken1));
    }

    public function testGetLogoutLink()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getShopHomeURL', 'isSsl']);
        $oCfg->expects($this->once())
            ->method('getShopHomeURL')
            ->will($this->returnValue('shopHomeUrl/'));
        $oCfg->expects($this->once())
            ->method('isSsl')
            ->will($this->returnValue(false));

        Registry::set(Config::class, $oCfg);

        $oVC = $this->getMock(
            'oxviewconfig',
            ['getTopActionClassName', 'getActCatId', 'getActTplName', 'getActContentLoadId', 'getActArticleId', 'getActSearchParam', 'getActSearchTag', 'getActListType', 'getActRecommendationId']
        );

        $oVC->expects($this->once())
            ->method('getTopActionClassName')
            ->will($this->returnValue('actionclass'));
        $oVC->expects($this->once())
            ->method('getActCatId')
            ->will($this->returnValue('catid'));
        $oVC->expects($this->once())
            ->method('getActTplName')
            ->will($this->returnValue('tpl'));
        $oVC->expects($this->once())
            ->method('getActContentLoadId')
            ->will($this->returnValue('oxloadid'));
        $oVC->expects($this->once())
            ->method('getActArticleId')
            ->will($this->returnValue('anid'));
        $oVC->expects($this->once())
            ->method('getActSearchParam')
            ->will($this->returnValue('searchparam'));
        $oVC->expects($this->once())
            ->method('getActRecommendationId')
            ->will($this->returnValue('testrecomm'));
        $oVC->expects($this->once())
            ->method('getActListType')
            ->will($this->returnValue('listtype'));

        $this->assertEquals('shopHomeUrl/cl=actionclass&amp;cnid=catid&amp;anid=anid&amp;searchparam=searchparam&amp;recommid=testrecomm&amp;listtype=listtype&amp;fnc=logout&amp;tpl=tpl&amp;oxloadid=oxloadid&amp;redirect=1', $oVC->getLogoutLink());
    }

    /**
     * Tests forming of logout link when in ssl page
     */
    public function testGetLogoutLinkSsl()
    {
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getShopSecureHomeUrl', 'isSsl']);
        $oCfg->expects($this->once())
            ->method('getShopSecureHomeUrl')
            ->will($this->returnValue('sslShopHomeUrl/'));
        $oCfg->expects($this->once())
            ->method('isSsl')
            ->will($this->returnValue(true));

        Registry::set(Config::class, $oCfg);

        $oVC = $this->getMock(
            'oxviewconfig',
            ['getTopActionClassName', 'getActCatId', 'getActTplName', 'getActContentLoadId', 'getActArticleId', 'getActSearchParam', 'getActSearchTag', 'getActListType', 'getActRecommendationId']
        );

        $oVC->expects($this->once())
            ->method('getTopActionClassName')
            ->will($this->returnValue('actionclass'));
        $oVC->expects($this->once())
            ->method('getActCatId')
            ->will($this->returnValue('catid'));
        $oVC->expects($this->once())
            ->method('getActTplName')
            ->will($this->returnValue('tpl'));
        $oVC->expects($this->once())
            ->method('getActContentLoadId')
            ->will($this->returnValue('oxloadid'));
        $oVC->expects($this->once())
            ->method('getActArticleId')
            ->will($this->returnValue('anid'));
        $oVC->expects($this->once())
            ->method('getActSearchParam')
            ->will($this->returnValue('searchparam'));
        $oVC->expects($this->once())
            ->method('getActRecommendationId')
            ->will($this->returnValue('testrecomm'));
        $oVC->expects($this->once())
            ->method('getActListType')
            ->will($this->returnValue('listtype'));

        $this->assertEquals('sslShopHomeUrl/cl=actionclass&amp;cnid=catid&amp;anid=anid&amp;searchparam=searchparam&amp;recommid=testrecomm&amp;listtype=listtype&amp;fnc=logout&amp;tpl=tpl&amp;oxloadid=oxloadid&amp;redirect=1', $oVC->getLogoutLink());
    }

    /**
     * check config params getter
     */
    public function testGetActionClassName()
    {
        $oV = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ['getActionClassName']);
        $oV->expects($this->once())
            ->method('getActionClassName')
            ->will($this->returnValue('lalala'));
        $oCfg = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getActiveView']);
        $oCfg->expects($this->once())
            ->method('getActiveView')
            ->will($this->returnValue($oV));
        $viewConfig = oxNew(ViewConfig::class);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oCfg);
        $this->assertEquals('lalala', $viewConfig->getActionClassName());
    }

    /**
     * oxViewConfig::getTopActionClassName() test case
     */
    public function testGetTopActionClassName()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["getClassKey"]);
        $oView->expects($this->once())->method("getClassKey")->will($this->returnValue("testViewClass"));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getTopActiveView"]);
        $oConfig->expects($this->once())->method("getTopActiveView")->will($this->returnValue($oView));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $viewConfig = oxNew(ViewConfig::class);
        $this->assertEquals("testViewClass", $viewConfig->getTopActiveClassName());
    }

    public function testGetShowBasketTimeoutWhenFunctionalityIsOnAndTimeLeft()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', ['getTimeLeft']);
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(5));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $session->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oVC = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $this->assertEquals(true, $oVC->getShowBasketTimeout());
    }

    public function testGetShowBasketTimeoutWhenFunctionalityIsOnAndTimeExpired()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', ['getTimeLeft']);
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(0));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $session->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oVC = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $this->assertEquals(false, $oVC->getShowBasketTimeout());
    }

    public function testGetShowBasketTimeoutWhenFunctionalityIsOff()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $oVC = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $this->assertEquals(false, $oVC->getShowBasketTimeout());
    }

    public function testGetBasketTimeLeft()
    {
        $oR = $this->getMock('stdclass', ['getTimeLeft']);
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(954));

        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['getBasketReservations']);
        $session->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oVC = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);

        $this->assertEquals(954, $oVC->getBasketTimeLeft());
        // return cached
        $this->assertEquals(954, $oVC->getBasketTimeLeft());
    }

    /**
     * test method
     *
     * return null
     */
    public function testIsTplBlocksDebugMode()
    {
        $myConfig = $this->getConfig();

        $oViewCfg = oxNew(ViewConfig::class);

        $myConfig->setConfigParam("blDebugTemplateBlocks", false);
        $this->assertFalse($oViewCfg->isTplBlocksDebugMode());
        $myConfig->setConfigParam("blDebugTemplateBlocks", true);
        $this->assertTrue($oViewCfg->isTplBlocksDebugMode());
    }

    /**
     * test method "getNrOfCatArticles()"
     *
     * return null
     */
    public function testGetNrOfCatArticles()
    {
        $aNrofCatArticlesInGrid = [1, 2, 3];
        $aNrofCatArticles = [4, 5, 6];

        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("aNrofCatArticlesInGrid", $aNrofCatArticlesInGrid);
        $myConfig->setConfigParam("aNrofCatArticles", $aNrofCatArticles);

        $oViewCfg = oxNew(ViewConfig::class);
        $oSession = $this->getSession();

        $myConfig->setConfigParam('sDefaultListDisplayType', 'grid');
        $this->assertEquals($aNrofCatArticlesInGrid, $oViewCfg->getNrOfCatArticles());

        $oSession->setVariable("ldtype", "grid");
        $this->assertEquals($aNrofCatArticlesInGrid, $oViewCfg->getNrOfCatArticles());

        $oSession->setVariable("ldtype", "line");
        $this->assertEquals($aNrofCatArticles, $oViewCfg->getNrOfCatArticles());

        $oSession->setVariable("ldtype", "infogrid");
        $this->assertEquals($aNrofCatArticles, $oViewCfg->getNrOfCatArticles());
    }

    /**
     * Testing oxViewConfig::getCountryList()
     */
    public function testGetCountryList()
    {
        $oView = oxNew('oxViewConfig');
        $this->assertTrue($oView->getCountryList() instanceof countrylist);
    }

    /**
     * test return value of getModulePath method
     *
     * @return void
     */
    public function testGetModulePath()
    {
        $viewConfig = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);

        $this->assertEquals($this->getTestModuleAssetsDirectory() . "/out", $viewConfig->getModulePath('testModule', 'out'));
        $this->assertEquals($this->getTestModuleAssetsDirectory() . "/out/", $viewConfig->getModulePath('testModule', '/out/'));

        $this->assertEquals(
            $this->getTestModuleAssetsDirectory() . "/some.css",
            $viewConfig->getModulePath('testModule', 'some.css')
        );

        $this->assertEquals(
            $this->getTestModuleAssetsDirectory() . "/some.css",
            $viewConfig->getModulePath('testModule', '/some.css')
        );
    }

    /**
     * test that a exception with a specific error message is thrown if the requested file is not found
     * (only in debug mode)
     *
     * @return void
     */
    public function testGetModulePathExceptionThrownWhenPathNotFoundAndDebugEnabled()
    {
        $config = Registry::getConfig();
        $config->setConfigParam('iDebug', -1);

        $config->getConfigParam('sShopDir');
        $message = "Requested file not found for module testModule";
        $this->expectException(\OxidEsales\EshopCommunity\Core\Exception\FileException::class);
        $this->expectExceptionMessage($message);

        $viewConfig = oxNew(ViewConfig::class);
        $viewConfig->getModulePath('testModule', '/non_existing_template');
    }

    /**
     * Test that _no_ exception is thrown even if the file does not exist,
     * because shop should be stable against errors when running in production.
     * The error will be logged anyway.
     *
     * @return void
     */
    public function testGetModulePathNoExceptionThrownWhenPathNotFoundAndDebugDisabled()
    {
        Registry::getConfig()->setConfigParam("iDebug", 0);

        $viewConfig = oxNew(\OxidEsales\Eshop\Core\ViewConfig::class);
        $viewConfig->getModulePath('testModule', '/non_existing_template');

        $expectedExceptionClass = \OxidEsales\Eshop\Core\Exception\FileException::class;
        $this->assertLoggedException($expectedExceptionClass);
    }

    /**
     * test that get module url returns the correct url
     *
     * @return void
     */
    public function testGetModuleUrl()
    {
        $viewConfig = oxNew(ViewConfig::class);
        $config = Registry::getConfig();

        $baseUrl = $config->getCurrentShopUrl();
        $this->assertEquals($baseUrl . 'out/modules/testModule/out', $viewConfig->getModuleUrl('testModule', 'out'));
        $this->assertEquals($baseUrl . 'out/modules/testModule/out/', $viewConfig->getModuleUrl('testModule', '/out/'));
        $this->assertEquals(
            $baseUrl . 'out/modules/testModule/some.css',
            $viewConfig->getModuleUrl('testModule', 'some.css')
        );
        $this->assertEquals(
            $baseUrl . 'out/modules/testModule/some.css',
            $viewConfig->getModuleUrl('testModule', '/some.css')
        );
        $this->assertEquals($baseUrl . 'out/modules/testModule/', $viewConfig->getModuleUrl('testModule'));

        //test if the subject under test still generates a valid module url in admin mode
        $config->setAdminMode(true);
        $viewConfig->setAdminMode(true);
        $config->setConfigParam('sAdminDir', 'admin');

        //in our test environment the domain for admin area is the normal shopurl
        //When using subshops it is important that getModuleUrl does not return the subshopurl in admin mode
        //because of browser security restrictions take effect when loading resources from differt domains
        $adminUrlWithoutAdminPath = $baseUrl;
        $this->assertEquals(
            $adminUrlWithoutAdminPath . 'out/modules/testModule/some.css',
            $viewConfig->getModuleUrl('testModule', 'some.css')
        );

        //Test when sShopURL is set and not sSSLShopURL, nor sAdminSSLURL
        $config->setConfigParam('sSSLShopURL', '');
        $config->setConfigParam('sAdminSSLURL', '');
        $config->setConfigParam('sShopURL', 'http://shop.localhost.local/');
        $this->assertEquals("http://shop.localhost.local/out/modules/testModule/", $viewConfig->getModuleUrl('testModule'));

        //Test when sSSLShopURL is set and sAdminSSLURL is not set
        $config->setIsSsl(true);
        $config->setConfigParam('sSSLShopURL', 'https://shop.localhost.local/');
        $this->assertEquals("https://shop.localhost.local/out/modules/testModule/", $viewConfig->getModuleUrl('testModule'));

        //Test if getModuleUrl returns the right url if adminssl url is set
        $config->setConfigParam('sAdminSSLURL', 'https://admin.localhost.local/admin/');
        $config->setIsSsl(true);
        //Next assert is only to guarantee excpected internal behavior to find problems faster
        $this->assertEquals("https://admin.localhost.local/admin/", $config->getCurrentShopUrl());
    }

    public function testGetModuleUrlExceptionThrownWhenPathNotFoundAndDebugEnabled()
    {
        Registry::getConfig()->setConfigParam("iDebug", -1);

        $viewConfig = oxNew(ViewConfig::class);

        $this->expectException(\OxidEsales\Eshop\Core\Exception\FileException::class);
        $viewConfig->getModuleUrl('testModule', '/non_existing_template');
    }

    /**
     * @covers \OxidEsales\EshopCommunity\Core\ViewConfig::getViewThemeParam
     */
    public function testGetViewThemeParamReadsDirectlyFromConfig()
    {
        $configStub = $this->getMockBuilder(Config::class)
            ->setMethods(['isThemeOption'])
            ->getMock();
        $configStub->method('isThemeOption')->willReturn('true');
        $configStub->setConfigParam('someParameter', 'someValue');
        Registry::set(Config::class, $configStub);

        $viewConfig = oxNew(ViewConfig::class);
        $this->assertEquals('someValue', $viewConfig->getViewThemeParam('someParameter'));

        /** Set and read the value again to discover caching issues */
        $configStub->setConfigParam('someParameter', 'otherValue');
        $this->assertEquals('otherValue', $viewConfig->getViewThemeParam('someParameter'));
    }

    /**
     * Test case for oxViewConfig::showSelectLists()
     */
    public function testShowSelectLists()
    {
        $blExp = (bool) $this->getConfig()->getConfigParam('bl_perfLoadSelectLists');
        $oVC = oxNew('oxViewConfig');
        $this->assertEquals($blExp, $oVC->showSelectLists());
    }

    /**
     * Test case for oxViewConfig::showSelectListsInList()
     */
    public function testShowSelectListsInList()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectListsInAList', true);

        $oVC = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ['showSelectLists']);
        $oVC->expects($this->once())->method('showSelectLists')->will($this->returnValue(true));
        $this->assertTrue($oVC->showSelectListsInList());
    }

    /**
     * Test case for oxViewConfig::showSelectListsInList()
     */
    public function testShowSelectListsInListFalse()
    {
        $oVC = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ['showSelectLists']);
        $oVC->expects($this->once())->method('showSelectLists')->will($this->returnValue(false));
        $this->assertFalse($oVC->showSelectListsInList());
    }

    /**
     * Test case for oxViewConfig::showSelectListsInList()
     */
    public function testShowSelectListsInListDifferent()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectListsInAList', false);

        $oVC = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ['showSelectLists']);
        $oVC->expects($this->once())->method('showSelectLists')->will($this->returnValue(true));
        $this->assertFalse($oVC->showSelectListsInList());
    }

    /**
     * oxViewconfig::getImageUrl() test case
     */
    public function testGetImageUrl()
    {
        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getImageUrl"]);
        $oViewConf->expects($this->once())->method("getImageUrl")
            ->will($this->returnValue("shopUrl/out/theme/img/imgFile"));
        $this->assertEquals("shopUrl/out/theme/img/imgFile", $oViewConf->getImageUrl('imgFile'));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getImageUrl"]);
        $oViewConf->expects($this->once())->method("getImageUrl")->will($this->returnValue("shopUrl/out/theme/img/"));
        $this->assertEquals("shopUrl/out/theme/img/", $oViewConf->getImageUrl());
    }

    /**
     * Testing getSelfLink()
     */
    public function testGetSelfLink()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopHomeURL"]);
        $oConfig->expects($this->once())->method("getShopHomeURL")->will($this->returnValue("testShopUrl"));
        Registry::set(Config::class, $oConfig);

        $oViewConfig = oxNew(ViewConfig::class);

        $this->assertEquals("testShopUrl", $oViewConfig->getSelfLink());
    }

    /**
     * Testing getSslSelfLink()
     */
    public function testGetSslSelfLink()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopSecureHomeURL"]);
        $oConfig->expects($this->once())->method("getShopSecureHomeURL")->will($this->returnValue("testSecureShopUrl"));
        Registry::set(Config::class, $oConfig);

        $oViewConfig = oxNew(ViewConfig::class);

        $this->assertEquals("testSecureShopUrl", $oViewConfig->getSslSelfLink());
    }

    /**
     * Testing getSslSelfLink() - admin mode
     */
    public function testGetSslSelfLink_adminMode()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopSecureHomeURL"]);
        $oConfig->expects($this->never())->method("getShopSecureHomeURL");
        Registry::set(Config::class, $oConfig);

        $oViewConfig = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ['isAdmin', 'getSelfLink']);
        $oViewConfig->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $oViewConfig->expects($this->once())->method("getSelfLink")->will($this->returnValue("testShopUrl"));

        $this->assertEquals("testShopUrl", $oViewConfig->getSslSelfLink());
    }

    /**
     * Testing isAltImageServerConfigured() - nothing configured
     */
    public function testIsAltImageServerConfigured_none()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', '');
        $this->getConfig()->setConfigParam('sAltImageDir', '');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', '');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertFalse($oViewConfig->isAltImageServerConfigured());
    }

    /**
     * Testing isAltImageServerConfigured() - http url configured
     */
    public function testIsAltImageServerConfigured_httpurl()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', 'http://img.oxid-esales.com');
        $this->getConfig()->setConfigParam('sAltImageDir', '');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', '');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue($oViewConfig->isAltImageServerConfigured());
    }

    /**
     * Testing isAltImageServerConfigured() - http dir configured
     */
    public function testIsAltImageServerConfigured_httpdir()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', '');
        $this->getConfig()->setConfigParam('sAltImageDir', 'http://img.oxid-esales.com');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', '');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue($oViewConfig->isAltImageServerConfigured());
    }

    /**
     * Testing isAltImageServerConfigured() - https url configured
     */
    public function testIsAltImageServerConfigured_httpsurl()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', '');
        $this->getConfig()->setConfigParam('sAltImageDir', '');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', 'https://img.oxid-esales.com');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', '');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue($oViewConfig->isAltImageServerConfigured());
    }

    /**
     * Testing isAltImageServerConfigured() - https dir configured
     */
    public function testIsAltImageServerConfigured_httpsdir()
    {
        $this->getConfig()->setConfigParam('sAltImageUrl', '');
        $this->getConfig()->setConfigParam('sAltImageDir', '');
        $this->getConfig()->setConfigParam('sSSLAltImageUrl', '');
        $this->getConfig()->setConfigParam('sSSLAltImageDir', 'https://img.oxid-esales.com');

        $oViewConfig = oxNew('oxViewConfig');

        $this->assertTrue($oViewConfig->isAltImageServerConfigured());
    }

    /**
     * oxViewConfig::getTopActiveClassName() test case
     */
    public function testGetTopActiveClassName()
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["getClassKey"]);
        $oView->expects($this->once())->method("getClassKey")->will($this->returnValue("testViewClass"));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getTopActiveView"]);
        $oConfig->expects($this->once())->method("getTopActiveView")->will($this->returnValue($oView));

        Registry::set(Config::class, $oConfig);
        $oViewConfig = oxNew(ViewConfig::class);

        $this->assertEquals("testViewClass", $oViewConfig->getTopActiveClassName());
    }

    public function testIsFunctionalityEnabled()
    {
        $oConfig = $this->createMock(\OxidEsales\Eshop\Core\Config::class);
        $oConfig->expects($this->once())->method("getConfigParam")->with($this->equalTo('bl_showWishlist'))->will($this->returnValue("will"));

        $oViewConfig = oxNew(ViewConfig::class);

        Registry::set(Config::class, $oConfig);
        $this->assertTrue($oViewConfig->isFunctionalityEnabled('bl_showWishlist'));
    }

    /**
     * oxViewconfig::getActTplName() test case
     */
    public function testGetActTplName()
    {
        $this->setRequestParameter("tpl", 123);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals(123, $oViewConf->getActTplName());
    }

    /**
     * oxViewconfig::getActCurrency() test case
     */
    public function testGetActCurrency()
    {
        $this->setRequestParameter("cur", 1);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals(1, $oViewConf->getActCurrency());
    }

    /**
     * oxViewconfig::getActContentLoadId() test case
     */
    public function testGetActContentLoadId()
    {
        $this->setRequestParameter("oxloadid", 123);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals(123, $oViewConf->getActContentLoadId());

        $this->setRequestParameter("oxloadid", null);
        $oViewConf->setViewConfigParam('oxloadid', 234);
        $this->assertNull($oViewConf->getActContentLoadId());
    }

    /**
     * oxViewconfig::getActContentLoadId() test case
     */
    public function testGetActContentLoadIdFromActView()
    {
        $oView = oxNew('content');
        $oViewConf = $oView->getViewConfig();
        $oViewConf->setViewConfigParam('oxloadid', 234);

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getTopActiveView"]);
        $oConfig->expects($this->any())->method("getTopActiveView")->will($this->returnValue($oView));

        Registry::set(Config::class, $oConfig);
        $oViewConfig = oxNew(ViewConfig::class);

        $this->assertEquals(234, $oViewConfig->getActContentLoadId());
    }

    /**
     * oxViewconfig::getActRecommendationId() test case
     */
    public function testGetActRecommendationId()
    {
        $this->setRequestParameter("recommid", 1);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals(1, $oViewConf->getActRecommendationId());
    }

    /**
     * oxViewconfig::getActCatId() test case
     */
    public function testGetActCatId()
    {
        $iCat = 12345;
        $this->setRequestParameter("cnid", $iCat);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($iCat, $oViewConf->getActCatId());
    }

    /**
     * oxViewconfig::getActArticleId() test case
     */
    public function testGetActArticleId()
    {
        $sArt = "12345";
        $this->setRequestParameter("anid", $sArt);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($sArt, $oViewConf->getActArticleId());
    }

    /**
     * oxViewconfig::getActSearchParam() test case
     */
    public function testGetActSearchParam()
    {
        $sParam = "test=john";
        $this->setRequestParameter("searchparam", $sParam);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($sParam, $oViewConf->getActSearchParam());
    }

    /**
     * oxViewconfig::getActListType() test case
     */
    public function testGetActListType()
    {
        $sType = "testType";
        $this->setRequestParameter("listtype", $sType);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($sType, $oViewConf->getActListType());
    }

    /**
     * oxViewconfig::getContentId() test case
     */
    public function testGetContentId()
    {
        $sOxcid = "testCID";
        $this->setRequestParameter("oxcid", $sOxcid);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($sOxcid, $oViewConf->getContentId());
    }

    /**
     * oxViewconfig::getViewConfigParam() test case
     */
    public function testGetViewConfigParamFromOShop()
    {
        $sFieldName = "nameFromObject";

        $oShop = new stdClass();
        $oShop->$sFieldName = "testShopObj";

        $oViewConf = $this->getProxyClass('oxViewConfig');
        $oViewConf->setNonPublicVar('_oShop', $oShop);
        $this->assertEquals($oShop->$sFieldName, $oViewConf->getViewConfigParam($sFieldName));
    }

    /**
     * oxViewconfig::getViewConfigParam() test case
     */
    public function testGetViewConfigParamFromAViewData()
    {
        $sFieldName = "nameFromArray";

        $aViewData = [];
        $aViewData[$sFieldName] = "testShopArr";

        $oViewConf = $this->getProxyClass('oxViewConfig');
        $oViewConf->setNonPublicVar('_aViewData', $aViewData);
        $this->assertEquals($aViewData[$sFieldName], $oViewConf->getViewConfigParam($sFieldName));
    }

    /**
     * oxViewconfig::getHiddenSid() test case
     */
    public function testGetHiddenSidFromSession()
    {
        $sSid = "newSid";

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("hiddensid"))->will($this->returnValue($sSid));

        $this->assertEquals($sSid, $oViewConf->getHiddenSid());
    }

    /**
     * oxViewconfig::getHiddenSid() test case
     */
    public function testGetHiddenSidFromSessionNull()
    {
        $sSid = "newSid";
        $sLang = "testLang";
        $sSidNew = $sSid . '
' . $sLang;
        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ["hiddenSid"]);
        $session->expects($this->once())->method("hiddenSid")->will($this->returnValue($sSid));
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ["getFormLang"]);
        $oLang->expects($this->once())->method("getFormLang")->will($this->returnValue($sLang));
        Registry::set(\OxidEsales\Eshop\Core\Language::class, $oLang);

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("hiddensid"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("hiddensid"), $this->equalTo($sSidNew));

        $this->assertEquals($sSidNew, $oViewConf->getHiddenSid());
    }

    /**
     * oxViewconfig::getBaseDir() test case
     */
    public function testGetBaseDirForSsl()
    {
        $sSslLink = "sslsitelink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isSsl", "getSSLShopURL"]);
        $oConfig->expects($this->once())->method("isSsl")->will($this->returnValue(true));
        $oConfig->expects($this->once())->method("getSSLShopURL")->will($this->returnValue($sSslLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("basedir"))->will($this->returnValue(null));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sSslLink, $oViewConf->getBaseDir());
    }

    /**
     * oxViewconfig::getCoreUtilsDir() test case
     */
    public function testGetCoreUtilsDir()
    {
        $sDir = "testingDir";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('coreutilsdir', $sDir);

        $this->assertEquals($sDir, $oViewConf->getCoreUtilsDir());
    }

    /**
     * oxViewconfig::getCoreUtilsDir() test case
     */
    public function testGetCoreUtilsDirWhenNull()
    {
        $sDir = "testingDir";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getCoreUtilsURL"]);
        $oConfig->expects($this->once())->method("getCoreUtilsURL")->will($this->returnValue($sDir));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("coreutilsdir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("coreutilsdir"), $this->equalTo($sDir));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sDir, $oViewConf->getCoreUtilsDir());
    }

    /**
     * oxViewconfig::getSelfActionLink() test case
     */
    public function testGetSelfActionLink()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('selfactionlink', $sLink);

        $this->assertEquals($sLink, $oViewConf->getSelfActionLink());
    }

    /**
     * oxViewconfig::getSelfActionLink() test case
     */
    public function testGetSelfActionLinkWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopCurrentUrl"]);
        $oConfig->expects($this->once())->method("getShopCurrentUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("selfactionlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("selfactionlink"), $this->equalTo($sLink));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLink, $oViewConf->getSelfActionLink());
    }

    /**
     * oxViewconfig::getCurrentHomeDir() test case
     */
    public function testGetCurrentHomeDir()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('currenthomedir', $sLink);

        $this->assertEquals($sLink, $oViewConf->getCurrentHomeDir());
    }

    /**
     * oxViewconfig::getCurrentHomeDir() test case
     */
    public function testGetCurrentHomeDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getCurrentShopUrl"]);
        $oConfig->expects($this->once())->method("getCurrentShopUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("currenthomedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("currenthomedir"), $this->equalTo($sLink));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLink, $oViewConf->getCurrentHomeDir());
    }

    /**
     * oxViewconfig::getBasketLink() test case
     */
    public function testGetBasketLink()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('basketlink', $sLink);

        $this->assertEquals($sLink, $oViewConf->getBasketLink());
    }

    /**
     * oxViewconfig::getBasketLink() test case
     */
    public function testGetBasketLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = 'testingLinkcl=basket';
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopHomeURL"]);
        $oConfig->expects($this->once())->method("getShopHomeURL")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("basketlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("basketlink"), $this->equalTo($sLinkNew));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLinkNew, $oViewConf->getBasketLink());
    }

    /**
     * oxViewconfig::getOrderLink() test case
     */
    public function testGetOrderLink()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('orderlink', $sLink);

        $this->assertEquals($sLink, $oViewConf->getOrderLink());
    }

    /**
     * oxViewconfig::getOrderLink() test case
     */
    public function testGetOrderLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = 'testingLinkcl=user';
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopSecureHomeUrl"]);
        $oConfig->expects($this->once())->method("getShopSecureHomeUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("orderlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("orderlink"), $this->equalTo($sLinkNew));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLinkNew, $oViewConf->getOrderLink());
    }

    /**
     * oxViewconfig::getPaymentLink() test case
     */
    public function testGetPaymentLink()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('paymentlink', $sLink);

        $this->assertEquals($sLink, $oViewConf->getPaymentLink());
    }

    /**
     * oxViewconfig::getPaymentLink() test case
     */
    public function testGetPaymentLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = 'testingLinkcl=payment';
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopSecureHomeUrl"]);
        $oConfig->expects($this->once())->method("getShopSecureHomeUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("paymentlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("paymentlink"), $this->equalTo($sLinkNew));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLinkNew, $oViewConf->getPaymentLink());
    }

    /**
     * oxViewconfig::getExeOrderLink() test case
     */
    public function testGetExeOrderLink()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('exeorderlink', $sLink);

        $this->assertEquals($sLink, $oViewConf->getExeOrderLink());
    }

    /**
     * oxViewconfig::getExeOrderLink() test case
     */
    public function testGetExeOrderLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = 'testingLinkcl=order&amp;fnc=execute';
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopSecureHomeUrl"]);
        $oConfig->expects($this->once())->method("getShopSecureHomeUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("exeorderlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("exeorderlink"), $this->equalTo($sLinkNew));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLinkNew, $oViewConf->getExeOrderLink());
    }

    /**
     * oxViewconfig::getOrderConfirmLink() test case
     */
    public function testGetOrderConfirmLink()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('orderconfirmlink', $sLink);

        $this->assertEquals($sLink, $oViewConf->getOrderConfirmLink());
    }

    /**
     * oxViewconfig::getOrderConfirmLink() test case
     */
    public function testGetOrderConfirmLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = 'testingLinkcl=order';
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopSecureHomeUrl"]);
        $oConfig->expects($this->once())->method("getShopSecureHomeUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("orderconfirmlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("orderconfirmlink"), $this->equalTo($sLinkNew));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLinkNew, $oViewConf->getOrderConfirmLink());
    }

    /**
     * oxViewconfig::getResourceUrl() test case
     */
    public function testGetResourceUrl()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('basetpldir', $sLink);

        $this->assertEquals($sLink, $oViewConf->getResourceUrl());
    }

    /**
     * oxViewconfig::getResourceUrl() test case
     */
    public function testGetResourceUrlWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getResourceUrl"]);
        $oConfig->expects($this->once())->method("getResourceUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("basetpldir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("basetpldir"), $this->equalTo($sLink));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLink, $oViewConf->getResourceUrl());
    }

    /**
     * oxViewconfig::getResourceUrl() test case
     */
    public function testGetResourceUrlWithFile()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getResourceUrl"]);
        $oConfig->expects($this->once())->method("getResourceUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["setViewConfigParam"]);
        $oViewConf->expects($this->never())->method("setViewConfigParam");

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLink, $oViewConf->getResourceUrl($sLink));
    }

    /**
     * oxViewconfig::getTemplateDir() test case
     */
    public function testGetTemplateDir()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('templatedir', $sLink);

        $this->assertEquals($sLink, $oViewConf->getTemplateDir());
    }

    /**
     * oxViewconfig::getTemplateDir() test case
     */
    public function testGetTemplateDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getTemplateDir"]);
        $oConfig->expects($this->once())->method("getTemplateDir")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("templatedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("templatedir"), $this->equalTo($sLink));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLink, $oViewConf->getTemplateDir());
    }

    /**
     * oxViewconfig::getUrlTemplateDir() test case
     */
    public function testGetUrlTemplateDir()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('urltemplatedir', $sLink);

        $this->assertEquals($sLink, $oViewConf->getUrlTemplateDir());
    }

    /**
     * oxViewconfig::getTemplateDir() test case
     */
    public function testGetUrlTemplateDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getTemplateUrl"]);
        $oConfig->expects($this->once())->method("getTemplateUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("urltemplatedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("urltemplatedir"), $this->equalTo($sLink));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLink, $oViewConf->getUrlTemplateDir());
    }

    /**
     * oxViewconfig::getNoSslImageDir() test case
     */
    public function testGetNoSslImageDir()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('nossl_imagedir', $sLink);

        $this->assertEquals($sLink, $oViewConf->getNoSslImageDir());
    }

    /**
     * oxViewconfig::getNoSslImageDir() test case
     */
    public function testGetNoSslImageDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getImageUrl"]);
        $oConfig->expects($this->once())->method("getImageUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("nossl_imagedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("nossl_imagedir"), $this->equalTo($sLink));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLink, $oViewConf->getNoSslImageDir());
    }

    /**
     * oxViewconfig::getPictureDir() test case
     */
    public function testGetPictureDir()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('picturedir', $sLink);

        $this->assertEquals($sLink, $oViewConf->getPictureDir());
    }

    /**
     * oxViewconfig::getPictureDir() test case
     */
    public function testGetPictureDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getPictureUrl"]);
        $oConfig->expects($this->once())->method("getPictureUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("picturedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("picturedir"), $this->equalTo($sLink));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sLink, $oViewConf->getPictureDir());
    }

    /**
     * oxViewconfig::getAdminDir() test case
     */
    public function testGetAdminDir()
    {
        $sLink = "testingLink";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('sAdminDir', $sLink);

        $this->assertEquals($sLink, $oViewConf->getAdminDir());
    }

    /**
     * oxViewconfig::getAdminDir() test case
     */
    public function testGetAdminDirWhenNull()
    {
        $sLink = "testingLink";
        $this->getConfig()->setConfigParam("sAdminDir", $sLink);

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("sAdminDir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("sAdminDir"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getAdminDir());
    }

    /**
     * oxViewconfig::getActiveShopId() test case
     */
    public function testGetActiveShopId()
    {
        $sId = "testShopId";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('shopid', $sId);

        $this->assertEquals($sId, $oViewConf->getActiveShopId());
    }

    /**
     * oxViewconfig::getActiveShopId() test case
     */
    public function testGetActiveShopIdWhenNull()
    {
        $sId = "testShopId";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopId"]);
        $oConfig->expects($this->once())->method("getShopId")->will($this->returnValue($sId));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("shopid"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("shopid"), $this->equalTo($sId));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sId, $oViewConf->getActiveShopId());
    }

    /**
     * oxViewconfig::isSsl() test case
     */
    public function testIsSsl()
    {
        $sTest = "isSsl";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('isssl', $sTest);

        $this->assertEquals($sTest, $oViewConf->isSsl());
    }

    /**
     * oxViewconfig::isSsl() test case
     */
    public function testIsSslWhenNull()
    {
        $sTest = "isSsl";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isSsl"]);
        $oConfig->expects($this->once())->method("isSsl")->will($this->returnValue($sTest));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("isssl"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("isssl"), $this->equalTo($sTest));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sTest, $oViewConf->isSsl());
    }

    /**
     * oxViewconfig::getRemoteAddress() test case
     */
    public function testGetRemoteAddress()
    {
        $sTest = "testAddress";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('ip', $sTest);

        $this->assertEquals($sTest, $oViewConf->getRemoteAddress());
    }

    /**
     * oxViewconfig::getRemoteAddress() test case
     */
    public function testGetRemoteAddressWhenNull()
    {
        $sTest = "testAddress";

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ["getRemoteAddress"]);
        $oUtils->expects($this->once())->method("getRemoteAddress")->will($this->returnValue($sTest));

        Registry::set(\OxidEsales\Eshop\Core\UtilsServer::class, $oUtils);

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("ip"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("ip"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getRemoteAddress());
    }

    /**
     * oxViewconfig::getPopupIdent() test case
     */
    public function testGetPopupIdent()
    {
        $sTest = "testIdent";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('popupident', $sTest);

        $this->assertEquals($sTest, $oViewConf->getPopupIdent());
    }

    /**
     * oxViewconfig::getPopupIdent() test case
     */
    public function testGetPopupIdentWhenNull()
    {
        $sTest = "testIdent";
        $sTestNew = md5($sTest);
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopUrl"]);
        $oConfig->expects($this->once())->method("getShopUrl")->will($this->returnValue($sTest));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("popupident"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("popupident"), $this->equalTo($sTestNew));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sTestNew, $oViewConf->getPopupIdent());
    }

    /**
     * oxViewconfig::getPopupIdentRand() test case
     */
    public function testGetPopupIdentRand()
    {
        $sTest = "testIdent";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('popupidentrand', $sTest);

        $this->assertEquals($sTest, $oViewConf->getPopupIdentRand());
    }

    /**
     * oxViewconfig::getPopupIdentRand() test case
     */
    public function testGetPopupIdentRandWhenNull()
    {
        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("popupidentrand"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("popupidentrand"));

        $this->assertTrue(strlen((string) $oViewConf->getPopupIdentRand()) == 32);
    }

    /**
     * oxViewconfig::getArtPerPageForm() test case
     */
    public function testGetArtPerPageForm()
    {
        $sTest = "testUrl";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('artperpageform', $sTest);

        $this->assertEquals($sTest, $oViewConf->getArtPerPageForm());
    }

    /**
     * oxViewconfig::getArtPerPageForm() test case
     */
    public function testGetArtPerPageFormWhenNull()
    {
        $sTest = "testUrl";
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getShopCurrentUrl"]);
        $oConfig->expects($this->once())->method("getShopCurrentUrl")->will($this->returnValue($sTest));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["getViewConfigParam", "setViewConfigParam"]);
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("artperpageform"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("artperpageform"), $this->equalTo($sTest));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($sTest, $oViewConf->getArtPerPageForm());
    }

    /**
     * oxViewconfig::isBuyableParent() test case
     */
    public function testIsBuyableParent()
    {
        $this->getConfig()->setConfigParam("blVariantParentBuyable", true);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertTrue($oViewConf->isBuyableParent());

        $this->getConfig()->setConfigParam("blVariantParentBuyable", false);
        $this->assertFalse($oViewConf->isBuyableParent());
    }

    /**
     * oxViewconfig::showBirthdayFields() test case
     */
    public function testShowBirthdayFields()
    {
        $this->getConfig()->setConfigParam("blShowBirthdayFields", true);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertTrue($oViewConf->showBirthdayFields());

        $this->getConfig()->setConfigParam("blShowBirthdayFields", false);
        $this->assertFalse($oViewConf->showBirthdayFields());
    }

    /**
     * oxViewconfig::getActLanguageAbbr() test case
     */
    public function testGetActLanguageAbbr()
    {
        $sTest = "testAbc";

        $oLang = $this->getMock(\OxidEsales\Eshop\Core\Language::class, ["getLanguageAbbr"]);
        $oLang->expects($this->once())->method("getLanguageAbbr")->will($this->returnValue($sTest));

        Registry::set(\OxidEsales\Eshop\Core\Language::class, $oLang);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($sTest, $oViewConf->getActLanguageAbbr());
    }

    /**
     * oxViewconfig::getActiveClassName() test case
     */
    public function testGetActiveClassName()
    {
        $sTest = "testAbc";

        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["getClassKey"]);
        $oView->expects($this->once())->method("getClassKey")->will($this->returnValue($sTest));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getActiveView"]);
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        Registry::set(Config::class, $oConfig);
        $oViewConf = oxNew(ViewConfig::class);

        $this->assertEquals($sTest, $oViewConf->getActiveClassName());
    }


    /**
     * oxViewconfig::getArtPerPageCount() test case
     */
    public function testGetArtPerPageCount()
    {
        $sTest = "testAbc";
        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('iartPerPage', $sTest);

        $this->assertEquals($sTest, $oViewConf->getArtPerPageCount());
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     */
    public function testGetNavUrlParams()
    {
        $sTest = "testAbc";
        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('navurlparams', $sTest);

        $this->assertEquals($sTest, $oViewConf->getNavUrlParams());
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @dataProvider providerGetNavUrlParamsNavigation
     */
    public function testGetNavUrlParamsNavigation($paramsArray, $paramsString)
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["getNavigationParams"]);
        $oView->expects($this->atLeastOnce())->method("getNavigationParams")->will($this->returnValue($paramsArray));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getActiveView"]);
        $oConfig->expects($this->atLeastOnce())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["setViewConfigParam"]);
        $oViewConf->expects($this->atLeastOnce())->method("setViewConfigParam")->with($this->equalTo("navurlparams"), $this->equalTo($paramsString));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($paramsString, $oViewConf->getNavUrlParams());
    }

    public function providerGetNavUrlParamsNavigation()
    {
        return [
            'empty params'         => [
                [],
                ''
            ],
            'one param'            => [
                ["testKey" => "testValue"],
                "&amp;testKey=testValue"
            ],
            'two params'           => [
                ["testKey1" => "testValue1", "testKey2" => "testValue2"],
                "&amp;testKey1=testValue1&amp;testKey2=testValue2"
            ],
            'two params one empty' => [
                ["testKey1" => "testValue1", "testKey2" => null],
                "&amp;testKey1=testValue1"
            ]
        ];
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     */
    public function getNavFormParams()
    {
        $sTest = "testAbc";
        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('navformparams', $sTest);

        $this->assertEquals($sTest, $oViewConf->getNavFormParams());
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @dataProvider providerGetNavFormParams
     */
    public function testGetNavFormParams($paramsArray, $paramsFormControls)
    {
        $oView = $this->getMock(\OxidEsales\Eshop\Core\Controller\BaseController::class, ["getNavigationParams"]);
        $oView->expects($this->atLeastOnce())->method("getNavigationParams")->will($this->returnValue($paramsArray));

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getActiveView"]);
        $oConfig->expects($this->atLeastOnce())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock(\OxidEsales\Eshop\Core\ViewConfig::class, ["setViewConfigParam"]);
        $oViewConf->expects($this->atLeastOnce())->method("setViewConfigParam")->with($this->equalTo("navformparams"), $this->equalTo($paramsFormControls));

        Registry::set(Config::class, $oConfig);

        $this->assertEquals($paramsFormControls, $oViewConf->getNavFormParams());
    }

    public function providerGetNavFormParams()
    {
        return [
            'empty params'         => [
                [],
                ''
            ],
            'one param'            => [
                ["testKey" => "testVal"],
                '<input type="hidden" name="testKey" value="testVal" />' . PHP_EOL
            ],
            'two params'           => [
                ["testKey1" => "testVal1", "testKey2" => "testVal2"],
                '<input type="hidden" name="testKey1" value="testVal1" />' . PHP_EOL
                . '<input type="hidden" name="testKey2" value="testVal2" />' . PHP_EOL
            ],
            'two params one empty' => [
                ["testKey1" => "testVal1", "testKey2" => null],
                '<input type="hidden" name="testKey1" value="testVal1" />' . PHP_EOL
            ]
        ];
    }

    /**
     * oxViewconfig::getStockOnDefaultMessage() test case
     */
    public function testGetStockOnDefaultMessage()
    {
        $sTest = "testValue";
        $this->getConfig()->setConfigParam("blStockOnDefaultMessage", $sTest);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($sTest, $oViewConf->getStockOnDefaultMessage());
    }

    /**
     * oxViewconfig::getStockOffDefaultMessage() test case
     */
    public function testGetStockOffDefaultMessage()
    {
        $sTest = "testValue";
        $this->getConfig()->setConfigParam("blStockOffDefaultMessage", $sTest);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($sTest, $oViewConf->getStockOffDefaultMessage());
    }

    /**
     * oxViewconfig::getShopVersion() test case
     */
    public function testGetShopVersion()
    {
        $sTest = "testShopVersion";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('sShopVersion', $sTest);
        $this->assertEquals($sTest, $oViewConf->getShopVersion());
    }

    /**
     * oxViewconfig::isMultiShop() test case
     */
    public function testIsMultiShop()
    {
        $sTest = "testServiceUrl";

        $oObj = new stdClass();
        $oObj->oxshops__oxismultishop = new stdClass();
        $oObj->oxshops__oxismultishop->value = $sTest;

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getActiveShop"]);
        $oConfig->expects($this->once())->method("getActiveShop")->will($this->returnValue($oObj));

        Registry::set(Config::class, $oConfig);
        $oViewConf = oxNew(ViewConfig::class);

        $this->assertTrue($oViewConf->isMultiShop());
    }

    /**
     * oxViewconfig::isMultiShop() test case
     */
    public function testIsMultiShopNotSet()
    {
        $oObj = new stdClass();
        $oObj->oxshops__oxismultishop = null;

        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getActiveShop"]);
        $oConfig->expects($this->once())->method("getActiveShop")->will($this->returnValue($oObj));

        Registry::set(Config::class, $oConfig);
        $oViewConf = oxNew(ViewConfig::class);

        $this->assertFalse($oViewConf->isMultiShop());
    }

    /**
     * oxViewconfig::getPasswordLength() test case
     */
    public function testGetPasswordLength()
    {
        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals(6, $oViewConf->getPasswordLength());

        $this->getConfig()->setConfigParam("iPasswordLength", 66);
        $this->assertEquals(66, $oViewConf->getPasswordLength());
    }

    /**
     * oxViewconfig::getActiveTheme() test case for main theme
     */
    public function testGetActiveTheme_mainTheme()
    {
        $oViewConf = oxNew('oxViewConfig');
        Registry::getConfig()->setConfigParam("sTheme", "testTheme");
        $this->assertEquals('testTheme', $oViewConf->getActiveTheme());
    }

    /**
     * oxViewconfig::getActiveTheme() test case for custom theme
     */
    public function testGetActiveTheme_customTheme()
    {
        $oViewConf = oxNew('oxViewConfig');
        Registry::getConfig()->setConfigParam("sCustomTheme", "testCustomTheme");
        Registry::getConfig()->setConfigParam("sTheme", "testTheme");
        $this->assertEquals('testCustomTheme', $oViewConf->getActiveTheme());
    }

    public function testSetGetShopLogo()
    {
        $oView = oxNew('oxViewConfig');
        $oView->setShopLogo("testlogo");
        $this->assertEquals("testlogo", $oView->getShopLogo());
    }

    public function testSetGetShopLogo_FromConfig()
    {
        $oView = oxNew('oxViewConfig');
        $this->getConfig()->setConfigParam("sShopLogo", 'logo');
        $this->assertEquals("logo", $oView->getShopLogo());
    }

    public function testSetGetShopLogo_DefaultValue()
    {
        $oView = oxNew('oxViewConfig');

        $edition = strtolower((string) $this->getTestConfig()->getShopEdition());
        $sLogo = sprintf('logo_%s.png', $edition);

        $this->assertEquals($sLogo, $oView->getShopLogo());
    }

    /**
     * Data provider for test testGetSessionChallengeToken.
     *
     * @return array
     */
    public function _dpGetSessionChallengeToken()
    {
        return [[false, 0, ''], [true, 1, 'session_challenge_token']];
    }

    /**
     * /**
     * Tests retrieve session challenge token from session.
     *
     *
     * @dataProvider _dpGetSessionChallengeToken
     *
     * @param boolean $isSessionStarted Was session started.
     * @param integer $callTimes        method How many times getSessionChallengeToken is expected to be called.
     * @param string  $token            Security token.
     */
    #[\PHPUnit\Framework\Attributes\Group('getsessionchallengetoken')]
    public function testGetSessionChallengeToken($isSessionStarted, $callTimes, $token)
    {
        /** @var oxSession|PHPUnit\Framework\MockObject\MockObject $session */
        $session = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSessionStarted', 'getSessionChallengeToken']);

        $session->expects($this->once())->method('isSessionStarted')->will($this->returnValue($isSessionStarted));
        $session->expects($this->exactly($callTimes))->method('getSessionChallengeToken')->will($this->returnValue($token));
        Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        /** @var oxViewConfig $viewConfig */
        $viewConfig = oxNew('oxViewConfig');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $session);

        $this->assertSame($token, $viewConfig->getSessionChallengeToken());
    }

    public function testGetEdition()
    {
        $oViewConfig = oxNew('oxViewConfig');
        $this->assertEquals((new Facts())->getEdition(), $oViewConfig->getEdition());
    }

    private function addTestModuleAssets(): void
    {
        $this->removeTestModuleAssets();

        $filesystem = $this->getFilesystem();

        $filesystem->mkdir(Path::join(
            $this->getTestModuleAssetsDirectory(),
            'out'
        ));

        $filesystem->touch(
            Path::join(
                $this->getTestModuleAssetsDirectory(),
                'some.css'
            )
        );
    }

    private function removeTestModuleAssets(): void
    {
        $this->getFilesystem()->remove($this->getTestModuleAssetsDirectory());
    }

    private function getTestModuleAssetsDirectory(): string
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get(ModuleAssetsPathResolverBridgeInterface::class)
            ->getAssetsPath('testModule');
    }

    private function getFilesystem(): Filesystem
    {
        return ContainerFactory::getInstance()
            ->getContainer()
            ->get('oxid_esales.symfony.file_system');
    }
}
