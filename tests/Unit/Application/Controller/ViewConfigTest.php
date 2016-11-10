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

use OxidEsales\EshopCommunity\Application\Model\CountryList;

use \stdClass;
use \oxRegistry;
use \oxTestModules;

class ViewConfigTest extends \OxidTestCase
{
    /**
     * oxViewConfig::getHelpPageLink() test case
     *
     * @return null
     */
    public function testGetHelpPageLink()
    {
        $sShopUrl = $this->getConfig()->getConfigParam("sShopURL");

        $oViewConfig = $this->getMock("oxviewconfig", array("getActiveClassName"));
        $oViewConfig->expects($this->once())->method("getActiveClassName")->will($this->returnValue("start"));
        $this->assertEquals($sShopUrl . "Hilfe-Die-Startseite/", $oViewConfig->getHelpPageLink());

        $oViewConfig = $this->getMock("oxviewconfig", array("getActiveClassName"));
        $oViewConfig->expects($this->once())->method("getActiveClassName")->will($this->returnValue("alist"));
        $this->assertEquals($sShopUrl . "Hilfe-Die-Produktliste/", $oViewConfig->getHelpPageLink());

        $oViewConfig = $this->getMock("oxviewconfig", array("getActiveClassName"));
        $oViewConfig->expects($this->once())->method("getActiveClassName")->will($this->returnValue("details"));
        $this->assertEquals($sShopUrl . "Hilfe-Main/", $oViewConfig->getHelpPageLink());
    }

    /**
     * Check what happens when no help CMS content is found
     */
    public function testGetHelpPageLinkInactiveContents()
    {
        $oViewConfig = $this->getMock("oxviewconfig", array('_getHelpContentIdents'));
        $oViewConfig->expects($this->once())->method("_getHelpContentIdents")->will($this->returnValue(array("none")));
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
    public function testGetHomeLinkDataProvider()
    {
        $sShopUrl = $this->getConfig()->getShopUrl();

        $iLangDE = 0;
        $iLangEN = 1;

        // Parameters:
        // - default shop language
        // - default browser language
        // - expected URL
        return array(
            array($iLangDE, $iLangDE, $sShopUrl),
            array($iLangDE, $iLangEN, $sShopUrl . "index.php?lang=$iLangDE&amp;"),
            array($iLangEN, $iLangDE, $sShopUrl . "index.php?lang=1&amp;"),
            array($iLangEN, $iLangEN, $sShopUrl)
        );
    }

    /**
     * Test case for getting eShop home link in different default languages and browser default languages
     *
     * @param int    $iDefaultShopLanguage    default shop language
     * @param int    $iDefaultBrowserLanguage default browser language
     * @param string $sExpectedUrl            expected URL
     *
     * @dataProvider testGetHomeLinkDataProvider
     */
    public function testGetHomeLink($iDefaultShopLanguage, $iDefaultBrowserLanguage, $sExpectedUrl)
    {
        /** @var $oLang oxLang | PHPUnit_Framework_MockObject_MockObject */
        $oLang = $this->getMock('oxLang', array('detectLanguageByBrowser'));
        $oLang
            ->expects($this->any())
            ->method('detectLanguageByBrowser')
            ->will($this->returnValue($iDefaultBrowserLanguage));

        oxRegistry::set('oxLang', $oLang);

        oxTestModules::addFunction("oxutilsserver", "getServerVar", "{ \$aArgs = func_get_args(); if ( \$aArgs[0] === 'HTTP_HOST' ) { return '" . $this->getConfig()->getShopUrl() . "'; } elseif ( \$aArgs[0] === 'SCRIPT_NAME' ) { return ''; } else { return \$_SERVER[\$aArgs[0]]; } }");

        $this->setLanguage($iDefaultShopLanguage);
        $this->setConfigParam('sDefaultLang', $iDefaultShopLanguage);

        $oViewConfig = oxNew('oxViewConfig');
        $this->assertEquals(
            $sExpectedUrl,
            $oViewConfig->getHomeLink(),
            "URL is correct
            when default shop language is $iDefaultShopLanguage
            and default browser language is $iDefaultBrowserLanguage"
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
        $oCfg = $this->getMock('oxconfig', array('getConfigParam'));
        $oCfg->expects($this->once())
            ->method('getConfigParam')
            ->with($this->equalTo('bl_showWishlist'))
            ->will($this->returnValue('lalala'));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getShowWishlist());
    }

    /**
     * check config params getter
     */
    public function testGetShowCompareList()
    {
        $oView = $this->getMock('oxview', array('getIsOrderStep'));
        $oView->expects($this->once())->method('getIsOrderStep')->will($this->returnValue(true));

        $oCfg = $this->getMock('oxconfig', array('getConfigParam', 'getActiveView'));
        $oCfg->expects($this->at(0))->method('getConfigParam')->with($this->equalTo('bl_showCompareList'))->will($this->returnValue(true));
        $oCfg->expects($this->at(1))->method('getConfigParam')->with($this->equalTo('blDisableNavBars'))->will($this->returnValue(true));
        $oCfg->expects($this->at(2))->method('getActiveView')->will($this->returnValue($oView));

        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())->method('getConfig')->will($this->returnValue($oCfg));
        $this->assertFalse($oVC->getShowCompareList());
    }

    /**
     * check config params getter
     */
    public function testGetShowListmania()
    {
        $oCfg = $this->getMock('oxconfig', array('getConfigParam'));
        $oCfg->expects($this->once())
            ->method('getConfigParam')
            ->with($this->equalTo('bl_showListmania'))
            ->will($this->returnValue('lalala'));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getShowListmania());
    }

    /**
     * check config params getter
     */
    public function testGetShowVouchers()
    {
        $oCfg = $this->getMock('oxconfig', array('getConfigParam'));
        $oCfg->expects($this->once())
            ->method('getConfigParam')
            ->with($this->equalTo('bl_showVouchers'))
            ->will($this->returnValue('lalala'));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getShowVouchers());
    }

    /**
     * check config params getter
     */
    public function testGetShowGiftWrapping()
    {
        $oCfg = $this->getMock('oxconfig', array('getConfigParam'));
        $oCfg->expects($this->once())
            ->method('getConfigParam')
            ->with($this->equalTo('bl_showGiftWrapping'))
            ->will($this->returnValue('lalala'));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getShowGiftWrapping());
    }

    public function testGetRemoteAccessToken()
    {
        $oSubj = oxNew('oxViewConfig');
        $sTestToken1 = $oSubj->getRemoteAccessToken();
        $sTestToken2 = $oSubj->getRemoteAccessToken();

        $this->assertEquals($sTestToken1, $sTestToken2);
        $this->assertEquals(8, strlen($sTestToken1));
    }

    public function testGetLogoutLink()
    {
        $oCfg = $this->getMock('oxconfig', array('getShopHomeURL', 'isSsl'));
        $oCfg->expects($this->once())
            ->method('getShopHomeURL')
            ->will($this->returnValue('shopHomeUrl/'));
        $oCfg->expects($this->once())
            ->method('isSsl')
            ->will($this->returnValue(false));

        $oVC = $this->getMock(
            'oxviewconfig'
            , array('getConfig', 'getTopActionClassName', 'getActCatId', 'getActTplName', 'getActContentLoadId'
                    , 'getActArticleId', 'getActSearchParam', 'getActSearchTag', 'getActListType', 'getActRecommendationId')
        );

        $oVC->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($oCfg));
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
     *
     * @return null
     */
    public function testGetLogoutLinkSsl()
    {
        $oCfg = $this->getMock('oxconfig', array('getShopSecureHomeUrl', 'isSsl'));
        $oCfg->expects($this->once())
            ->method('getShopSecureHomeUrl')
            ->will($this->returnValue('sslShopHomeUrl/'));
        $oCfg->expects($this->once())
            ->method('isSsl')
            ->will($this->returnValue(true));

        $oVC = $this->getMock(
            'oxviewconfig'
            , array('getConfig', 'getTopActionClassName', 'getActCatId', 'getActTplName', 'getActContentLoadId'
                    , 'getActArticleId', 'getActSearchParam', 'getActSearchTag', 'getActListType', 'getActRecommendationId')
        );

        $oVC->expects($this->any())
            ->method('getConfig')
            ->will($this->returnValue($oCfg));
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
        $oV = $this->getMock('oxview', array('getActionClassName'));
        $oV->expects($this->once())
            ->method('getActionClassName')
            ->will($this->returnValue('lalala'));
        $oCfg = $this->getMock('oxconfig', array('getActiveView'));
        $oCfg->expects($this->once())
            ->method('getActiveView')
            ->will($this->returnValue($oV));
        $oVC = $this->getMock('oxviewconfig', array('getConfig'));
        $oVC->expects($this->once())
            ->method('getConfig')
            ->will($this->returnValue($oCfg));
        $this->assertEquals('lalala', $oVC->getActionClassName());
    }

    /**
     * oxViewConfig::getTopActionClassName() test case
     *
     * @return null
     */
    public function testGetTopActionClassName()
    {
        $oView = $this->getMock("oxView", array("getClassName"));
        $oView->expects($this->once())->method("getClassName")->will($this->returnValue("testViewClass"));

        $oConfig = $this->getMock("oxConfig", array("getTopActiveView"));
        $oConfig->expects($this->once())->method("getTopActiveView")->will($this->returnValue($oView));

        $oViewConfig = $this->getMock("oxViewConfig", array("getConfig"));
        $oViewConfig->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertEquals("testViewClass", $oViewConfig->getTopActiveClassName());
    }

    public function testGetShowBasketTimeoutWhenFunctionalityIsOnAndTimeLeft()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('getTimeLeft'));
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(5));

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oVC = $this->getMock('oxViewConfig', array('getSession'));
        $oVC->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertEquals(true, $oVC->getShowBasketTimeout());
    }

    public function testGetShowBasketTimeoutWhenFunctionalityIsOnAndTimeExpired()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', true);

        $oR = $this->getMock('stdclass', array('getTimeLeft'));
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(0));

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oVC = $this->getMock('oxViewConfig', array('getSession'));
        $oVC->expects($this->any())->method('getSession')->will($this->returnValue($oS));

        $this->assertEquals(false, $oVC->getShowBasketTimeout());
    }

    public function testGetShowBasketTimeoutWhenFunctionalityIsOff()
    {
        $this->getConfig()->setConfigParam('blPsBasketReservationEnabled', false);

        $oVC = $this->getMock('oxViewConfig', array('getSession'));
        $oVC->expects($this->never())->method('getSession');

        $this->assertEquals(false, $oVC->getShowBasketTimeout());
    }

    public function testGetBasketTimeLeft()
    {
        $oR = $this->getMock('stdclass', array('getTimeLeft'));
        $oR->expects($this->once())->method('getTimeLeft')->will($this->returnValue(954));

        $oS = $this->getMock('oxsession', array('getBasketReservations'));
        $oS->expects($this->once())->method('getBasketReservations')->will($this->returnValue($oR));

        $oVC = $this->getMock('oxViewConfig', array('getSession'));
        $oVC->expects($this->any())->method('getSession')->will($this->returnValue($oS));

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

        $oViewCfg = $this->getMock('oxViewConfig', array('getConfig'));
        $oViewCfg->expects($this->any())->method('getConfig')->will($this->returnValue($myConfig));

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
        $aNrofCatArticlesInGrid = array(1, 2, 3);
        $aNrofCatArticles = array(4, 5, 6);

        $myConfig = $this->getConfig();
        $myConfig->setConfigParam("aNrofCatArticlesInGrid", $aNrofCatArticlesInGrid);
        $myConfig->setConfigParam("aNrofCatArticles", $aNrofCatArticles);

        $oViewCfg = $this->getMock('oxViewConfig', array('getConfig'));
        $oViewCfg->expects($this->any())->method('getConfig')->will($this->returnValue($myConfig));

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
     *
     * @return null
     */
    public function testGetCountryList()
    {
        $oView = oxNew('oxViewConfig');
        $this->assertTrue($oView->getCountryList() instanceof countrylist);
    }

    public function testGetModulePath()
    {
        $config = $this->getConfig();
        $fakeShopDirectory = $this->createModuleStructure();
        $config->setConfigParam("sShopDir", $fakeShopDirectory . "/");

        /** @var oxViewConfig|PHPUnit_Framework_MockObject_MockObject $viewConfig */
        $viewConfig = $this->getMock('oxViewConfig', array('getConfig'));
        $viewConfig->expects($this->any())->method('getConfig')->will($this->returnValue($config));

        $this->assertEquals($fakeShopDirectory . "/modules/test1/out", $viewConfig->getModulePath('test1', 'out'));
        $this->assertEquals($fakeShopDirectory . "/modules/test1/out/", $viewConfig->getModulePath('test1', '/out/'));

        $this->assertEquals($fakeShopDirectory . "/modules/test1/out/blocks/test2.tpl", $viewConfig->getModulePath('test1', 'out/blocks/test2.tpl'));
        $this->assertEquals($fakeShopDirectory . "/modules/test1/out/blocks/test2.tpl", $viewConfig->getModulePath('test1', '/out/blocks/test2.tpl'));
    }

    public function testGetModulePathExceptionThrownWhenPathNotFoundAndDebugEnabled()
    {
        $config = $this->getConfig();
        $config->setConfigParam("iDebug", -1);

        $fakeShopDirectory = $this->createModuleStructure();
        $config->setConfigParam("sShopDir", $fakeShopDirectory);

        $message = "Requested file not found for module test1 (" . $fakeShopDirectory . "modules/test1/out/blocks/non_existing_template.tpl)";
        $this->setExpectedException('\OxidEsales\EshopCommunity\Core\Exception\FileException', $message);

        /** @var oxViewConfig|PHPUnit_Framework_MockObject_MockObject $viewConfig */
        $viewConfig = $this->getMock('oxViewConfig', array('getConfig'));
        $viewConfig->expects($this->any())->method('getConfig')->will($this->returnValue($config));

        $viewConfig->getModulePath('test1', '/out/blocks/non_existing_template.tpl');
    }

    public function testGetModulePathExceptionThrownWhenPathNotFoundAndDebugDisabled()
    {
        $config = $this->getConfig();
        $config->setConfigParam("iDebug", 0);

        $fakeShopDirectory = $this->createModuleStructure();
        $config->setConfigParam("sShopDir", $fakeShopDirectory . "/");

        /** @var oxViewConfig|PHPUnit_Framework_MockObject_MockObject $viewConfig */
        $viewConfig = $this->getMock('oxViewConfig', array('getConfig'));
        $viewConfig->expects($this->any())->method('getConfig')->will($this->returnValue($config));

        $this->assertEquals('', $viewConfig->getModulePath('test1', '/out/blocks/non_existing_template.tpl'));
    }

    public function testGetModuleUrl()
    {
        $config = $this->getConfig();
        $config->setConfigParam("iDebug", -1);

        $fakeShopDirectory = $this->createModuleStructure();
        $config->setConfigParam("sShopDir", $fakeShopDirectory);

        /** @var oxViewConfig|PHPUnit_Framework_MockObject_MockObject $viewConfig */
        $viewConfig = $this->getMock('oxViewConfig', array('getConfig'));
        $viewConfig->expects($this->any())->method('getConfig')->will($this->returnValue($config));

        $baseUrl = $config->getCurrentShopUrl();
        $this->assertEquals("{$baseUrl}modules/test1/out", $viewConfig->getModuleUrl('test1', 'out'));
        $this->assertEquals("{$baseUrl}modules/test1/out/", $viewConfig->getModuleUrl('test1', '/out/'));
        $this->assertEquals("{$baseUrl}modules/test1/out/blocks/test2.tpl", $viewConfig->getModuleUrl('test1', 'out/blocks/test2.tpl'));
        $this->assertEquals("{$baseUrl}modules/test1/out/blocks/test2.tpl", $viewConfig->getModuleUrl('test1', '/out/blocks/test2.tpl'));
    }

    public function testGetModuleUrlExceptionThrownWhenPathNotFoundAndDebugEnabled()
    {
        $config = $this->getConfig();
        $config->setConfigParam("iDebug", -1);

        $fakeShopDirectory = $this->createModuleStructure();
        $config->setConfigParam("sShopDir", $fakeShopDirectory);

        $message = "Requested file not found for module test1 (" . $fakeShopDirectory . "modules/test1/out/blocks/non_existing_template.tpl)";
        $this->setExpectedException('oxFileException', $message);

        /** @var oxViewConfig|PHPUnit_Framework_MockObject_MockObject $viewConfig */
        $viewConfig = $this->getMock('oxViewConfig', array('getConfig'));
        $viewConfig->expects($this->any())->method('getConfig')->will($this->returnValue($config));

        $viewConfig->getModuleUrl('test1', '/out/blocks/non_existing_template.tpl');
    }

    public function testViewThemeParam()
    {
        $oVC = oxNew('oxViewConfig');

        $oV = $this->getMock('oxConfig', array('isThemeOption'));
        $oV->expects($this->any())->method('getSession')->will($this->returnValue(false));

        $this->assertEquals(false, $oVC->getViewThemeParam('aaa'));

        $oV = $this->getMock('oxConfig', array('isThemeOption'));
        $oV->expects($this->any())->method('getSession')->will($this->returnValue(true));

        $this->getConfig()->setConfigParam('bl_showListmania', 1);
        $this->assertEquals(1, $oVC->getViewThemeParam('bl_showListmania'));

        $this->getConfig()->setConfigParam('bl_showListmania', 0);
        $this->assertEquals(0, $oVC->getViewThemeParam('bl_showListmania'));
    }

    /**
     * Test case for oxViewConfig::showSelectLists()
     *
     * @return null
     */
    public function testShowSelectLists()
    {
        $blExp = (bool) $this->getConfig()->getConfigParam('bl_perfLoadSelectLists');
        $oVC = oxNew('oxViewConfig');
        $this->assertEquals($blExp, $oVC->showSelectLists());
    }

    /**
     * Test case for oxViewConfig::showSelectListsInList()
     *
     * @return null
     */
    public function testShowSelectListsInList()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectListsInAList', true);

        $oVC = $this->getMock('oxviewconfig', array('showSelectLists'));
        $oVC->expects($this->once())->method('showSelectLists')->will($this->returnValue(true));
        $this->assertTrue($oVC->showSelectListsInList());
    }

    /**
     * Test case for oxViewConfig::showSelectListsInList()
     *
     * @return null
     */
    public function testShowSelectListsInListFalse()
    {
        $oVC = $this->getMock('oxviewconfig', array('showSelectLists'));
        $oVC->expects($this->once())->method('showSelectLists')->will($this->returnValue(false));
        $this->assertFalse($oVC->showSelectListsInList());
    }

    /**
     * Test case for oxViewConfig::showSelectListsInList()
     *
     * @return null
     */
    public function testShowSelectListsInListDifferent()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectListsInAList', false);

        $oVC = $this->getMock('oxviewconfig', array('showSelectLists'));
        $oVC->expects($this->once())->method('showSelectLists')->will($this->returnValue(true));
        $this->assertFalse($oVC->showSelectListsInList());
    }

    /**
     * oxViewconfig::getImageUrl() test case
     *
     * @return null
     */
    public function testGetImageUrl()
    {
        $oViewConf = $this->getMock("oxConfig", array("getImageUrl"));
        $oViewConf->expects($this->once())->method("getImageUrl")->will($this->returnValue("shopUrl/out/theme/img/imgFile"));
        $this->assertEquals("shopUrl/out/theme/img/imgFile", $oViewConf->getImageUrl('imgFile'));

        $oViewConf = $this->getMock("oxConfig", array("getImageUrl"));
        $oViewConf->expects($this->once())->method("getImageUrl")->will($this->returnValue("shopUrl/out/theme/img/"));
        $this->assertEquals("shopUrl/out/theme/img/", $oViewConf->getImageUrl());
    }

    /**
     * Testing getSelfLink()
     */
    public function testGetSelfLink()
    {
        $oConfig = $this->getMock("oxConfig", array("getShopHomeURL"));
        $oConfig->expects($this->once())->method("getShopHomeURL")->will($this->returnValue("testShopUrl"));

        $oViewConfig = $this->getMock('oxViewConfig', array('getConfig'));
        $oViewConfig->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals("testShopUrl", $oViewConfig->getSelfLink());
    }

    /**
     * Testing getSslSelfLink()
     */
    public function testGetSslSelfLink()
    {
        $oConfig = $this->getMock("oxConfig", array("getShopSecureHomeURL"));
        $oConfig->expects($this->once())->method("getShopSecureHomeURL")->will($this->returnValue("testSecureShopUrl"));

        $oViewConfig = $this->getMock('oxViewConfig', array('getConfig'));
        $oViewConfig->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));

        $this->assertEquals("testSecureShopUrl", $oViewConfig->getSslSelfLink());
    }

    /**
     * Testing getSslSelfLink() - admin mode
     */
    public function testGetSslSelfLink_adminMode()
    {
        $oConfig = $this->getMock("oxConfig", array("getShopSecureHomeURL"));
        $oConfig->expects($this->never())->method("getShopSecureHomeURL");

        $oViewConfig = $this->getMock('oxViewConfig', array('getConfig', 'isAdmin', 'getSelfLink'));
        $oViewConfig->expects($this->any())->method('getConfig')->will($this->returnValue($oConfig));
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
     *
     * @return null
     */
    public function testGetTopActiveClassName()
    {
        $oView = $this->getMock("oxView", array("getClassName"));
        $oView->expects($this->once())->method("getClassName")->will($this->returnValue("testViewClass"));

        $oConfig = $this->getMock("oxConfig", array("getTopActiveView"));
        $oConfig->expects($this->once())->method("getTopActiveView")->will($this->returnValue($oView));

        $oViewConfig = $this->getMock("oxViewConfig", array("getConfig"));
        $oViewConfig->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertEquals("testViewClass", $oViewConfig->getTopActiveClassName());
    }

    public function testIsFunctionalityEnabled()
    {
        $oConfig = $this->getMock("oxConfig", array("getConfigParam"));
        $oConfig->expects($this->once())->method("getConfigParam")->with($this->equalTo('bl_showWishlist'))->will($this->returnValue("will"));

        $oVieConfig = $this->getMock("oxViewConfig", array("getConfig"));
        $oVieConfig->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertTrue($oVieConfig->isFunctionalityEnabled('bl_showWishlist'));
    }

    /**
     * oxViewconfig::getActTplName() test case
     *
     * @return null
     */
    public function testGetActTplName()
    {
        $this->setRequestParameter("tpl", 123);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals(123, $oViewConf->getActTplName());
    }

    /**
     * oxViewconfig::getActCurrency() test case
     *
     * @return null
     */
    public function testGetActCurrency()
    {
        $this->setRequestParameter("cur", 1);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals(1, $oViewConf->getActCurrency());
    }

    /**
     * oxViewconfig::getActContentLoadId() test case
     *
     * @return null
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
     *
     * @return null
     */
    public function testGetActContentLoadIdFromActView()
    {
        $oView = oxNew('content');
        $oViewConf = $oView->getViewConfig();
        $oViewConf->setViewConfigParam('oxloadid', 234);

        $oConfig = $this->getMock("oxConfig", array("getTopActiveView"));
        $oConfig->expects($this->any())->method("getTopActiveView")->will($this->returnValue($oView));

        $oViewConfig = $this->getMock("oxViewConfig", array("getConfig"));
        $oViewConfig->expects($this->any())->method("getConfig")->will($this->returnValue($oConfig));
        $this->assertEquals(234, $oViewConfig->getActContentLoadId());
    }

    /**
     * oxViewconfig::getActRecommendationId() test case
     *
     * @return null
     */
    public function testGetActRecommendationId()
    {
        $this->setRequestParameter("recommid", 1);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals(1, $oViewConf->getActRecommendationId());
    }

    /**
     * oxViewconfig::getActCatId() test case
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetViewConfigParamFromAViewData()
    {
        $sFieldName = "nameFromArray";

        $aViewData = array();
        $aViewData[$sFieldName] = "testShopArr";

        $oViewConf = $this->getProxyClass('oxViewConfig');
        $oViewConf->setNonPublicVar('_aViewData', $aViewData);
        $this->assertEquals($aViewData[$sFieldName], $oViewConf->getViewConfigParam($sFieldName));
    }

    /**
     * oxViewconfig::getHiddenSid() test case
     *
     * @return null
     */

    public function testGetHiddenSidFromSession()
    {
        $sSid = "newSid";

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getSession"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("hiddensid"))->will($this->returnValue($sSid));
        $oViewConf->expects($this->never())->method("getSession");

        $this->assertEquals($sSid, $oViewConf->getHiddenSid());
    }

    /**
     * oxViewconfig::getHiddenSid() test case
     *
     * @return null
     */

    public function testGetHiddenSidFromSessionNull()
    {
        $sSid = "newSid";
        $sLang = "testLang";
        $sSidNew = $sSid . '
' . $sLang;
        $oSession = $this->getMock("oxSession", array("hiddenSid"));
        $oSession->expects($this->once())->method("hiddenSid")->will($this->returnValue($sSid));

        $oLang = $this->getMock("oxLang", array("getFormLang"));
        $oLang->expects($this->once())->method("getFormLang")->will($this->returnValue($sLang));
        oxRegistry::set("oxLang", $oLang);

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getSession", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("hiddensid"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getSession")->will($this->returnValue($oSession));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("hiddensid"), $this->equalTo($sSidNew));

        $this->assertEquals($sSidNew, $oViewConf->getHiddenSid());
    }

    /**
     * oxViewconfig::getBaseDir() test case
     *
     * @return null
     */

    public function testGetBaseDirForSsl()
    {
        $sSslLink = "sslsitelink";
        $oConfig = $this->getMock("oxConfig", array("isSsl", "getSSLShopURL"));
        $oConfig->expects($this->once())->method("isSsl")->will($this->returnValue(true));
        $oConfig->expects($this->once())->method("getSSLShopURL")->will($this->returnValue($sSslLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("basedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->exactly(2))->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertEquals($sSslLink, $oViewConf->getBaseDir());
    }

    /**
     * oxViewconfig::getCoreUtilsDir() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetCoreUtilsDirWhenNull()
    {
        $sDir = "testingDir";
        $oConfig = $this->getMock("oxConfig", array("getCoreUtilsURL"));
        $oConfig->expects($this->once())->method("getCoreUtilsURL")->will($this->returnValue($sDir));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("coreutilsdir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("coreutilsdir"), $this->equalTo($sDir));

        $this->assertEquals($sDir, $oViewConf->getCoreUtilsDir());
    }

    /**
     * oxViewconfig::getSelfActionLink() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetSelfActionLinkWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock("oxConfig", array("getShopCurrentUrl"));
        $oConfig->expects($this->once())->method("getShopCurrentUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("selfactionlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("selfactionlink"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getSelfActionLink());
    }

    /**
     * oxViewconfig::getCurrentHomeDir() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetCurrentHomeDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock("oxConfig", array("getCurrentShopUrl"));
        $oConfig->expects($this->once())->method("getCurrentShopUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("currenthomedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("currenthomedir"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getCurrentHomeDir());
    }

    /**
     * oxViewconfig::getBasketLink() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetBasketLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = "testingLink" . "cl=basket";
        $oConfig = $this->getMock("oxConfig", array("getShopHomeURL"));
        $oConfig->expects($this->once())->method("getShopHomeURL")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("basketlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("basketlink"), $this->equalTo($sLinkNew));

        $this->assertEquals($sLinkNew, $oViewConf->getBasketLink());
    }

    /**
     * oxViewconfig::getOrderLink() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetOrderLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = "testingLink" . "cl=user";
        $oConfig = $this->getMock("oxConfig", array("getShopSecureHomeUrl"));
        $oConfig->expects($this->once())->method("getShopSecureHomeUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("orderlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("orderlink"), $this->equalTo($sLinkNew));

        $this->assertEquals($sLinkNew, $oViewConf->getOrderLink());
    }

    /**
     * oxViewconfig::getPaymentLink() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetPaymentLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = "testingLink" . "cl=payment";
        $oConfig = $this->getMock("oxConfig", array("getShopSecureHomeUrl"));
        $oConfig->expects($this->once())->method("getShopSecureHomeUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("paymentlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("paymentlink"), $this->equalTo($sLinkNew));

        $this->assertEquals($sLinkNew, $oViewConf->getPaymentLink());
    }

    /**
     * oxViewconfig::getExeOrderLink() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetExeOrderLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = "testingLink" . "cl=order&amp;fnc=execute";
        $oConfig = $this->getMock("oxConfig", array("getShopSecureHomeUrl"));
        $oConfig->expects($this->once())->method("getShopSecureHomeUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("exeorderlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("exeorderlink"), $this->equalTo($sLinkNew));

        $this->assertEquals($sLinkNew, $oViewConf->getExeOrderLink());
    }

    /**
     * oxViewconfig::getOrderConfirmLink() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetOrderConfirmLinkWhenNull()
    {
        $sLink = "testingLink";
        $sLinkNew = "testingLink" . "cl=order";
        $oConfig = $this->getMock("oxConfig", array("getShopSecureHomeUrl"));
        $oConfig->expects($this->once())->method("getShopSecureHomeUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("orderconfirmlink"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("orderconfirmlink"), $this->equalTo($sLinkNew));

        $this->assertEquals($sLinkNew, $oViewConf->getOrderConfirmLink());
    }

    /**
     * oxViewconfig::getResourceUrl() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetResourceUrlWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock("oxConfig", array("getResourceUrl"));
        $oConfig->expects($this->once())->method("getResourceUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("basetpldir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("basetpldir"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getResourceUrl());
    }

    /**
     * oxViewconfig::getResourceUrl() test case
     *
     * @return null
     */

    public function testGetResourceUrlWithFile()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock("oxConfig", array("getResourceUrl"));
        $oConfig->expects($this->once())->method("getResourceUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->never())->method("setViewConfigParam");

        $this->assertEquals($sLink, $oViewConf->getResourceUrl($sLink));
    }

    /**
     * oxViewconfig::getTemplateDir() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetTemplateDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock("oxConfig", array("getTemplateDir"));
        $oConfig->expects($this->once())->method("getTemplateDir")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("templatedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("templatedir"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getTemplateDir());
    }

    /**
     * oxViewconfig::getUrlTemplateDir() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetUrlTemplateDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock("oxConfig", array("getTemplateUrl"));
        $oConfig->expects($this->once())->method("getTemplateUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("urltemplatedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("urltemplatedir"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getUrlTemplateDir());
    }

    /**
     * oxViewconfig::getNoSslImageDir() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetNoSslImageDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock("oxConfig", array("getImageUrl"));
        $oConfig->expects($this->once())->method("getImageUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("nossl_imagedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("nossl_imagedir"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getNoSslImageDir());
    }

    /**
     * oxViewconfig::getPictureDir() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetPictureDirWhenNull()
    {
        $sLink = "testingLink";
        $oConfig = $this->getMock("oxConfig", array("getPictureUrl"));
        $oConfig->expects($this->once())->method("getPictureUrl")->will($this->returnValue($sLink));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("picturedir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("picturedir"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getPictureDir());
    }

    /**
     * oxViewconfig::getAdminDir() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetAdminDirWhenNull()
    {
        $sLink = "testingLink";
        $this->getConfig()->setConfigParam("sAdminDir", $sLink);

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("sAdminDir"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("sAdminDir"), $this->equalTo($sLink));

        $this->assertEquals($sLink, $oViewConf->getAdminDir());
    }

    /**
     * oxViewconfig::getActiveShopId() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetActiveShopIdWhenNull()
    {
        $sId = "testShopId";
        $oConfig = $this->getMock("oxConfig", array("getShopId"));
        $oConfig->expects($this->once())->method("getShopId")->will($this->returnValue($sId));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("shopid"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("shopid"), $this->equalTo($sId));

        $this->assertEquals($sId, $oViewConf->getActiveShopId());
    }

    /**
     * oxViewconfig::isSsl() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testIsSslWhenNull()
    {
        $sTest = "isSsl";
        $oConfig = $this->getMock("oxConfig", array("isSsl"));
        $oConfig->expects($this->once())->method("isSsl")->will($this->returnValue($sTest));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("isssl"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("isssl"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->isSsl());
    }

    /**
     * oxViewconfig::getRemoteAddress() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetRemoteAddressWhenNull()
    {
        $sTest = "testAddress";

        $oUtils = $this->getMock("oxUtilsServer", array("getRemoteAddress"));
        $oUtils->expects($this->once())->method("getRemoteAddress")->will($this->returnValue($sTest));

        oxRegistry::set("oxUtilsServer", $oUtils);

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("ip"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("ip"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getRemoteAddress());
    }

    /**
     * oxViewconfig::getPopupIdent() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetPopupIdentWhenNull()
    {
        $sTest = "testIdent";
        $sTestNew = md5($sTest);
        $oConfig = $this->getMock("oxConfig", array("getShopUrl"));
        $oConfig->expects($this->once())->method("getShopUrl")->will($this->returnValue($sTest));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("popupident"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("popupident"), $this->equalTo($sTestNew));

        $this->assertEquals($sTestNew, $oViewConf->getPopupIdent());
    }

    /**
     * oxViewconfig::getPopupIdentRand() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetPopupIdentRandWhenNull()
    {
        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("popupidentrand"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("popupidentrand"));

        $this->assertTrue(strlen($oViewConf->getPopupIdentRand()) == 32);
    }

    /**
     * oxViewconfig::getArtPerPageForm() test case
     *
     * @return null
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
     *
     * @return null
     */

    public function testGetArtPerPageFormWhenNull()
    {
        $sTest = "testUrl";
        $oConfig = $this->getMock("oxConfig", array("getShopCurrentUrl"));
        $oConfig->expects($this->once())->method("getShopCurrentUrl")->will($this->returnValue($sTest));

        $oViewConf = $this->getMock("oxViewConfig", array("getViewConfigParam", "getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getViewConfigParam")->with($this->equalTo("artperpageform"))->will($this->returnValue(null));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("artperpageform"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getArtPerPageForm());
    }

    /**
     * oxViewconfig::isBuyableParent() test case
     *
     * @return null
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
     *
     * @return null
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
     * oxViewconfig::showFinalStep() test case
     *
     * @return null
     */
    public function testShowFinalStep()
    {
        $oViewConf = oxNew('oxViewConfig');
        $this->assertTrue($oViewConf->showFinalStep());
    }

    /**
     * oxViewconfig::getActLanguageAbbr() test case
     *
     * @return null
     */
    public function testGetActLanguageAbbr()
    {
        $sTest = "testAbc";

        $oLang = $this->getMock("oxLang", array("getLanguageAbbr"));
        $oLang->expects($this->once())->method("getLanguageAbbr")->will($this->returnValue($sTest));

        oxRegistry::set("oxLang", $oLang);

        $oViewConf = oxNew('oxViewConfig');
        $this->assertEquals($sTest, $oViewConf->getActLanguageAbbr());
    }

    /**
     * oxViewconfig::getActiveClassName() test case
     *
     * @return null
     */
    public function testGetActiveClassName()
    {
        $sTest = "testAbc";

        $oView = $this->getMock("oxView", array("getClassName"));
        $oView->expects($this->once())->method("getClassName")->will($this->returnValue($sTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertEquals($sTest, $oViewConf->getActiveClassName());
    }


    /**
     * oxViewconfig::getArtPerPageCount() test case
     *
     * @return null
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
     *
     * @return null
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
     * @return null
     */
    public function testGetNavUrlParamsEmptyNavigationParams()
    {
        $aTest = array();
        $sTest = "";

        $oView = $this->getMock("oxView", array("getNavigationParams"));
        $oView->expects($this->once())->method("getNavigationParams")->will($this->returnValue($aTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("navurlparams"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getNavUrlParams());
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @return null
     */
    public function testGetNavUrlParamsOneNavigationParam()
    {
        $aTest = array("testKey" => "testValue");
        $sTest = "&amp;testKey=testValue";

        $oView = $this->getMock("oxView", array("getNavigationParams"));
        $oView->expects($this->once())->method("getNavigationParams")->will($this->returnValue($aTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("navurlparams"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getNavUrlParams());
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @return null
     */
    public function testGetNavUrlParamsTwoNavigationParams()
    {
        $aTest = array("testKey1" => "testValue1", "testKey2" => "testValue2");
        $sTest = "&amp;testKey1=testValue1&amp;testKey2=testValue2";

        $oView = $this->getMock("oxView", array("getNavigationParams"));
        $oView->expects($this->once())->method("getNavigationParams")->will($this->returnValue($aTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("navurlparams"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getNavUrlParams());
    }

    /**
     * oxViewconfig::getNavUrlParams() test case
     *
     * @return null
     */
    public function testGetNavUrlParamsTwoNavigationParamsOneWithoutValue()
    {
        $aTest = array("testKey1" => "testValue1", "testKey2" => null);
        $sTest = "&amp;testKey1=testValue1";

        $oView = $this->getMock("oxView", array("getNavigationParams"));
        $oView->expects($this->once())->method("getNavigationParams")->will($this->returnValue($aTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("navurlparams"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getNavUrlParams());
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
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
     * @return null
     */
    public function testGetNavFormParamsEmptyNavigationParams()
    {
        $aTest = array();
        $sTest = "";

        $oView = $this->getMock("oxView", array("getNavigationParams"));
        $oView->expects($this->once())->method("getNavigationParams")->will($this->returnValue($aTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("navformparams"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getNavFormParams());
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
     */
    public function testGetNavFormParamsOneNavigationParam()
    {
        $aTest = array("testKey" => "testVal");
        $sTest = '<input type="hidden" name="testKey" value="testVal" />
';

        $oView = $this->getMock("oxView", array("getNavigationParams"));
        $oView->expects($this->once())->method("getNavigationParams")->will($this->returnValue($aTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("navformparams"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getNavFormParams());
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
     */
    public function testGetNavFormParamsTwoNavigationParams()
    {
        $aTest = array("testKey1" => "testVal1", "testKey2" => "testVal2");
        $sTest = '<input type="hidden" name="testKey1" value="testVal1" />
<input type="hidden" name="testKey2" value="testVal2" />
';

        $oView = $this->getMock("oxView", array("getNavigationParams"));
        $oView->expects($this->once())->method("getNavigationParams")->will($this->returnValue($aTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("navformparams"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getNavFormParams());
    }

    /**
     * oxViewconfig::getNavFormParams() test case
     *
     * @return null
     */
    public function testGetNavFormParamsTwoNavigationParamsOneWithoutValue()
    {
        $aTest = array("testKey1" => "testVal1", "testKey2" => null);
        $sTest = '<input type="hidden" name="testKey1" value="testVal1" />
';

        $oView = $this->getMock("oxView", array("getNavigationParams"));
        $oView->expects($this->once())->method("getNavigationParams")->will($this->returnValue($aTest));

        $oConfig = $this->getMock("oxConfig", array("getActiveView"));
        $oConfig->expects($this->once())->method("getActiveView")->will($this->returnValue($oView));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig", "setViewConfigParam"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));
        $oViewConf->expects($this->once())->method("setViewConfigParam")->with($this->equalTo("navformparams"), $this->equalTo($sTest));

        $this->assertEquals($sTest, $oViewConf->getNavFormParams());
    }

    /**
     * oxViewconfig::getStockOnDefaultMessage() test case
     *
     * @return null
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
     *
     * @return null
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
     *
     * @return null
     */
    public function testGetShopVersion()
    {
        $sTest = "testShopVersion";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('sShopVersion', $sTest);
        $this->assertEquals($sTest, $oViewConf->getShopVersion());
    }

    /**
     * oxViewconfig::getServiceUrl() test case
     *
     * @return null
     */
    public function testGetServiceUrl()
    {
        $sTest = "testServiceUrl";

        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->setViewConfigParam('sServiceUrl', $sTest);
        $this->assertEquals($sTest, $oViewConf->getServiceUrl());
    }

    /**
     * oxViewconfig::isMultiShop() test case
     *
     * @return null
     */
    public function testIsMultiShop()
    {
        $sTest = "testServiceUrl";

        $oObj = new stdClass();
        $oObj->oxshops__oxismultishop = new stdClass();
        $oObj->oxshops__oxismultishop->value = $sTest;

        $oConfig = $this->getMock("oxConfig", array("getActiveShop"));
        $oConfig->expects($this->once())->method("getActiveShop")->will($this->returnValue($oObj));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertTrue($oViewConf->isMultiShop());
    }

    /**
     * oxViewconfig::isMultiShop() test case
     *
     * @return null
     */
    public function testIsMultiShopNotSet()
    {
        $oObj = new stdClass();
        $oObj->oxshops__oxismultishop = null;

        $oConfig = $this->getMock("oxConfig", array("getActiveShop"));
        $oConfig->expects($this->once())->method("getActiveShop")->will($this->returnValue($oObj));

        $oViewConf = $this->getMock("oxViewConfig", array("getConfig"));
        $oViewConf->expects($this->once())->method("getConfig")->will($this->returnValue($oConfig));

        $this->assertFalse($oViewConf->isMultiShop());
    }

    /**
     * oxViewconfig::getPasswordLength() test case
     *
     * @return null
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
        $oViewConf->getConfig()->setConfigParam("sTheme", "testTheme");
        $this->assertEquals('testTheme', $oViewConf->getActiveTheme());
    }

    /**
     * oxViewconfig::getActiveTheme() test case for custom theme
     */
    public function testGetActiveTheme_customTheme()
    {
        $oViewConf = oxNew('oxViewConfig');
        $oViewConf->getConfig()->setConfigParam("sCustomTheme", "testCustomTheme");
        $oViewConf->getConfig()->setConfigParam("sTheme", "testTheme");
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

        $edition = strtolower($this->getTestConfig()->getShopEdition());
        $sLogo = "logo_$edition.png";

        $this->assertEquals($sLogo, $oView->getShopLogo());
    }

    /**
     * Data provider for test testGetSessionChallengeToken.
     *
     * @return array
     */
    public function _dpGetSessionChallengeToken()
    {
        return array(
            array(false, 0, ''),
            array(true, 1, 'session_challenge_token'),
        );
    }

    /**
     * /**
     * Tests retrieve session challenge token from session.
     *
     * @dataProvider _dpGetSessionChallengeToken
     *
     * @param boolean $isSessionStarted Was session started.
     * @param integer $callTimes method How many times getSessionChallengeToken is expected to be called.
     * @param string  $token            Security token.
     */
    public function testGetSessionChallengeToken($isSessionStarted, $callTimes, $token)
    {
        /** @var oxSession|PHPUnit_Framework_MockObject_MockObject $session */
        $session = $this->getMock('oxSession', array('isSessionStarted', 'getSessionChallengeToken'));

        $session->expects($this->once())->method('isSessionStarted')->will($this->returnValue($isSessionStarted));
        $session->expects($this->exactly($callTimes))->method('getSessionChallengeToken')->will($this->returnValue($token));
        oxRegistry::set('oxSession', $session);

        /** @var oxViewConfig $viewConfig */
        $viewConfig = oxNew('oxViewConfig');
        $viewConfig->setSession($session);

        $this->assertSame($token, $viewConfig->getSessionChallengeToken());
    }

    /**
     * Module data provider.
     */
    public function _dpIsModuleActive()
    {
        return array(
            array(array('order' => 'oe/oepaypal/controllers/oepaypalorder'), array('oepaypal' => '2.0'), array(), 'oepaypal', true), // module activated
            array(array('order' => 'oe/oepaypal/controllers/oepaypalorder'), array(), array(0 => 'oepaypal'), 'oepaypal', false),    // module disabled
            array(array(), array(), array(), 'oepaypal', false),                                                                     // module never activated
            array(array(), array('oepaypal' => '2.0'), array(0 => 'oepaypal'), 'oepaypal', false),                                   // module does not extend oxid-class and disabled
        );
    }

    /**
     * oxViewConfig::oePayPalIsModuleActive()
     * @dataProvider _dpIsModuleActive
     */
    public function testIsModuleActive($aModules, $aModuleVersions, $aDisabledModules, $sModuleId, $blModuleIsActive)
    {

        $this->setConfigParam('aModules', $aModules);
        $this->setConfigParam('aDisabledModules', $aDisabledModules);
        $this->setConfigParam('aModuleVersions', $aModuleVersions);

        $oViewConf = oxNew('oxViewConfig');
        $blIsModuleActive = $oViewConf->isModuleActive($sModuleId);

        $this->assertEquals($blModuleIsActive, $blIsModuleActive, "Module state is not as expected.");
    }

    /**
     * Data provider
     *
     * @return array
     */
    public function providerIsModuleActive_VersionCheck()
    {
        return array(
            array('1.8', null, true),
            array('2.0', null, true),
            array('2.1', null, false),
            array('3.0', null, false),
            array(null, '1.8', false),
            array(null, '2.0', false),
            array(null, '2.1', true),
            array(null, '3.0', true),
            array('1.8', '3.0', true),
            array('1.0', '1.7', false),
            array('2.1', '3.0', false),
        );
    }

    /**
     * Testing isModuleAction version check
     *
     * @dataProvider providerIsModuleActive_VersionCheck
     */
    public function testIsModuleActive_VersionCheck($sFrom, $sTo, $blModuleStateExpected)
    {
        $aModules = array(
            'order' => 'oe/oepaypal/controllers/oepaypalorder',
            'order2' => 'oe/oepaypal2/controllers/oepaypalorder',
        );
        $aModuleVersions = array(
            'oepaypal' => '2.0',
            'oepaypal2' => '5.0'
        );
        $this->setConfigParam('aModules', $aModules);
        $this->setConfigParam('aDisabledModules', array());
        $this->setConfigParam('aModuleVersions', $aModuleVersions);

        $oViewConf = oxNew('oxViewConfig');
        $blIsModuleActive = $oViewConf->isModuleActive('oepaypal', $sFrom, $sTo);

        $this->assertEquals($blModuleStateExpected, $blIsModuleActive, "Module state is not from '$sFrom' to '$sTo'.");
    }

    public function testGetEdition()
    {
        $oViewConfig = oxNew('oxViewConfig');
        $this->assertEquals( $this->getConfig()->getEdition(), $oViewConfig->getEdition() );
    }

    /**
     * Creates module structre for testing.
     *
     * @return string Path to modules root.
     */
    private function createModuleStructure()
    {
        $structure = array(
            'modules' => array(
                'test1' => array(
                    'out' => array(
                        'blocks' => array(
                            'test2.tpl' => '*this is module test block*'
                        ),
                        'lang' => array(
                            'de' => array(
                                'test_lang.php' => ''
                            )
                        )
                    )
                )
            )
        );
        $vfsStream = $this->getVfsStreamWrapper();
        $vfsStream->createStructure($structure);

        return $vfsStream->getRootPath();
    }
}
