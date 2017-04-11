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

class Unit_Core_oxUtilsUrlTest extends OxidTestCase
{

    protected function setUp()
    {
        parent::setUp();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {

        parent::tearDown();
    }

    /**
     * oxUtilsUrl::prepareCanonicalUrl() test case
     *
     * @return null
     */
    public function testPrepareCanonicalUrl()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return false;}');
        modConfig::getInstance()->setConfigParam("sDefaultLang", 9);
        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $sExpUrl = "shop.com/index.php?param1=value1&amp;bonusid=111";


        $sExpUrl .= "&amp;lang={$iLang}";

        $oUtils = new oxUtilsUrl();
        $this->assertEquals($sExpUrl, $oUtils->prepareCanonicalUrl("shop.com/index.php?param1=value1&amp;bonusid=111&amp;sid=1234"));
    }

    /**
     * oxUtilsUrl::cleanUrl() test case
     *
     * @return null
     */
    public function testCleanUrl()
    {
        $oUtils = new oxUtilsUrl();
        $this->assertEquals("http://www.myoxideshop.com/index.php", $oUtils->cleanUrl("http://www.myoxideshop.com/index.php?param1=value1&param2=value2"));
        $this->assertEquals("http://www.myoxideshop.com/index.php?param2=value2", $oUtils->cleanUrl("http://www.myoxideshop.com/index.php?param1=value1&param2=value2", array("param1")));
    }


    public function testGetBaseAddUrlParamsPE()
    {

        $oUtils = new oxUtilsUrl();
        $this->assertEquals(array(), $oUtils->getBaseAddUrlParams());
    }


    public function testGetAddUrlParams()
    {
        modConfig::setRequestParameter("currency", 1);
        $aBaseUrlParams = array("param1" => "value1", "param2" => "value2");

        $oUtils = $this->getMock("oxUtilsUrl", array("getBaseAddUrlParams"));
        $oUtils->expects($this->once())->method('getBaseAddUrlParams')->will($this->returnValue($aBaseUrlParams));

        $aBaseUrlParams['cur'] = 1;
        $this->assertEquals($aBaseUrlParams, $oUtils->getAddUrlParams());
    }

    public function testPrepareUrlForNoSessionSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return true;}');

        $this->assertEquals('http://example.com/', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('http://example.com/?sid=abc123'));
        $this->assertEquals('http://example.com/', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('http://example.com/?force_sid=abc123'));

        $this->assertEquals('http://example.com/?cl=test', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('http://example.com/?cl=test&amp;sid=abc123'));
        $this->assertEquals('http://example.com/?cl=test', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('http://example.com/?cl=test&amp;force_sid=abc123'));

        $this->assertEquals('http://example.com/?cl=test', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('http://example.com/?sid=abc123&amp;cl=test'));
        $this->assertEquals('http://example.com/?cl=test', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('http://example.com/?force_sid=abc123&amp;cl=test'));
    }

    public function testPrepareUrlForNoSession()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return false;}');
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 3;}');

        $sShopId = '';

        $this->assertEquals('sdf?lang=1' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?sid=111&lang=1'));
        $this->assertEquals('sdf?a&lang=1' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?sid=111&a&lang=1'));
        $this->assertEquals('sdf?a&amp;lang=1' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?sid=111&a&amp;lang=1'));
        $this->assertEquals('sdf?a&&amp;lang=3' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?sid=111&a&'));
        $this->assertEquals('sdf?lang=3' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf'));

        $sShopId = '';
        $this->getConfig()->setShopId(5);

        $this->assertEquals('sdf?lang=3' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?sid=asd'));
        $this->assertEquals('sdf?lang=2' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?sid=das&lang=2'));
        $this->assertEquals('sdf?lang=2&shp=3', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?lang=2&sid=fs&amp;shp=3'));
        $this->assertEquals('sdf?shp=2&amp;lang=2', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?shp=2&amp;lang=2'));
        $this->assertEquals('sdf?shp=2&amp;lang=3', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?shp=2'));

        $this->assertEquals('sdf?lang=1' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?force_sid=111&lang=1'));
        $this->assertEquals('sdf?a&lang=1' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?force_sid=111&a&lang=1'));
        $this->assertEquals('sdf?a&amp;lang=1' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?force_sid=111&a&amp;lang=1'));
        $this->assertEquals('sdf?a&&amp;lang=3' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?force_sid=111&a&'));

        $this->assertEquals('sdf?bonusid=111&amp;lang=3' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?bonusid=111'));
        $this->assertEquals('sdf?a=1&bonusid=111&amp;lang=3' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?a=1&bonusid=111'));
        $this->assertEquals('sdf?a=1&amp;bonusid=111&amp;lang=3' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf?a=1&amp;bonusid=111&amp;force_admin_sid=111'));

        modConfig::getInstance()->setRequestParameter('currency', 2);
        $this->assertEquals('sdf?lang=3&amp;cur=2' . $sShopId, oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf'));

        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return true;}');
        $this->assertEquals('sdf', oxRegistry::get("oxUtilsUrl")->prepareUrlForNoSession('sdf'));
    }

    public function providerAppendUrl()
    {
        return array(
            array('testUrl', array(), 'testUrl?'),
            array('testUrl', array('p1' => 'v1', 'p2' => 'v2'), 'testUrl?p1=v1&amp;p2=v2&amp;'),
            array('testUrl?', array(), 'testUrl?'),
            array('testUrl?', array('p1' => 'v1', 'p2' => 'v2'), 'testUrl?p1=v1&amp;p2=v2&amp;'),
            array('testUrl?p1=v1', array('p2' => 'v2'), 'testUrl?p1=v1&amp;p2=v2&amp;'),
            array('testUrl?p1=v1&amp;', array('p2' => 'v2'), 'testUrl?p1=v1&amp;p2=v2&amp;'),
            array('testUrl?p1=v1&amp;', array(), 'testUrl?p1=v1&amp;'),
            array('testUrl?p1=v1&amp;', array('p1' => 'v1'), 'testUrl?p1=v1&amp;'),
        );
    }

    /**
     * @param string $sUrl
     * @param array  $aParams
     * @param string $sExtectedUrl
     *
     * @dataProvider providerAppendUrl
     */
    public function testAppendUrl($sUrl, $aParams, $sExtectedUrl)
    {
        $oUtils = new oxUtilsUrl();
        $this->assertEquals($sExtectedUrl, $oUtils->appendUrl($sUrl, $aParams));
    }

    public function providerAppendUrlWithFinalUrlForming()
    {
        return array(
            array('testUrl', array(), 'testUrl'),
            array('testUrl', array('p1' => 'v1', 'p2' => 'v2'), 'testUrl?p1=v1&amp;p2=v2'),
            array('testUrl?p1=v1', array('p2' => 'v2'), 'testUrl?p1=v1&amp;p2=v2'),
        );
    }

    /**
     * @param string $sUrl
     * @param array  $aParams
     * @param string $sExtectedUrl
     *
     * @dataProvider providerAppendUrlWithFinalUrlForming
     */
    public function testAppendUrlWithFinalUrlForming($sUrl, $aParams, $sExtectedUrl)
    {
        $oUtils = new oxUtilsUrl();
        $this->assertEquals($sExtectedUrl, $oUtils->appendUrl($sUrl, $aParams, true));
    }

    public function providerAddBaseUrl()
    {
        $sShopUrl = $this->getConfig()->getSslShopUrl();

        return array(
            array('http://external-url', 'http://external-url'),
            array('local-url', $sShopUrl . 'local-url'),
            array('?param1=value=1', $sShopUrl . '?param1=value=1'),
            array($sShopUrl, $sShopUrl),
            array($sShopUrl . '?param1=value1', $sShopUrl . '?param1=value1')
        );
    }

    /**
     * @dataProvider providerAddBaseUrl
     */
    public function testAddBaseUrl($sUrl, $sExpectedUrl)
    {
        $oUtils = new oxUtilsUrl();

        $this->assertEquals($sExpectedUrl, $oUtils->addShopHost($sUrl));
    }

    public function testProcessUrlWithParametersAdded()
    {
        $oUtils = new oxUtilsUrl();

        $aParameters = array('param1' => 'value1', 'param2' => 'value2');

        $sExpectedUrl = "http://some-url/?param1=value1&amp;param2=value2";
        $this->assertEquals($sExpectedUrl, $oUtils->processUrl("http://some-url/", true, $aParameters));
    }

    public function testProcessUrlWithAdditionalParametersAddedToLocalUrl()
    {
        $aParameters = array('param1' => 'value1', 'param2' => 'value2');

        $oUtils = $this->getMock('oxUtilsUrl', array('getBaseAddUrlParams'));
        $oUtils->expects($this->any())->method('getBaseAddUrlParams')->will($this->returnValue($aParameters));

        $sShopUrl = $this->getConfig()->getSslShopUrl();
        $sExpectedUrl = $sShopUrl . "?param1=value1&amp;param2=value2";
        $this->assertEquals($sExpectedUrl, $oUtils->processUrl($sShopUrl));
    }

    public function testProcessUrlWithAdditionalParametersNotAddedToExternalUrl()
    {
        $aParameters = array('param1' => 'value1', 'param2' => 'value2');

        $oUtils = $this->getMock('oxUtilsUrl', array('getBaseAddUrlParams'));
        $oUtils->expects($this->any())->method('getBaseAddUrlParams')->will($this->returnValue($aParameters));

        $this->assertEquals("http://some-url/", $oUtils->processUrl("http://some-url/"));
    }

    public function testProcessUrlWithLocalUrlLanguageShouldBeAdded()
    {
        $this->getConfig()->setConfigParam('sDefaultLang', 0);
        $this->setLanguage(1);

        $oUtils = new oxUtilsUrl();

        $sShopUrl = $this->getConfig()->getShopUrl();
        $this->assertEquals("$sShopUrl/anyUrl?lang=1", $oUtils->processUrl("$sShopUrl/anyUrl"));
    }

    public function testProcessUrlWithLocalUrlSIDShouldBeAdded()
    {
        /** @var oxSession $oSession */
        $oSession = $this->getMock('oxSession', array('isSidNeeded'));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->setId('SID');

        $oUtils = new oxUtilsUrl();
        oxRegistry::set('oxSession', $oSession);
        $sShopUrl = $this->getConfig()->getShopUrl();

        $this->assertEquals("$sShopUrl/anyUrl?force_sid=SID", $oUtils->processUrl("$sShopUrl/anyUrl"));
    }

    public function testProcessUrlWithRelativeUrlShouldActLikeLocal()
    {
        /** @var oxSession $oSession */
        $oSession = $this->getMock('oxSession', array('isSidNeeded'));
        $oSession->expects($this->any())->method('isSidNeeded')->will($this->returnValue(true));
        $oSession->setId('SID');

        $this->getConfig()->setConfigParam('sDefaultLang', 0);
        $this->setLanguage(1);

        $oUtils = new oxUtilsUrl();
        oxRegistry::set('oxSession', $oSession);

        $this->assertEquals("anyUrl?lang=1&amp;force_sid=SID", $oUtils->processUrl("anyUrl"));
    }

    public function testProcessUrlWithExternalUrlNoLanguageShouldBeAdded()
    {
        $this->getConfig()->setConfigParam('sDefaultLang', 0);
        $this->setLanguage(1);

        $oUtils = new oxUtilsUrl();

        $this->assertEquals("http://www.external-url.com/anyUrl", $oUtils->processUrl("http://www.external-url.com/anyUrl"));
    }

    public function testProcessUrlWithExternalUrlNoSIDShouldBeAdded()
    {
        $this->getSession()->setVariable('blSidNeeded', true);
        $this->getSession()->setId('SID');

        $oUtils = new oxUtilsUrl();

        $this->assertEquals("http://www.external-url.com/anyUrl", $oUtils->processUrl("http://www.external-url.com/anyUrl"));
    }

    public function testProcessUrlWithNonFinalUrl()
    {
        $this->getConfig()->setConfigParam('sDefaultLang', 0);
        $this->setLanguage(1);

        $oUtils = new oxUtilsUrl();

        $this->assertEquals("anyUrl?param=val1&amp;lang=1&amp;", $oUtils->processUrl("anyUrl", false, array('param' => 'val1')));
    }

    public function testAppendParamSeparator()
    {
        $oUtils = new oxUtilsUrl();
        $this->assertEquals("asd?", $oUtils->appendParamSeparator("asd"));
        $this->assertEquals("asd?", $oUtils->appendParamSeparator("asd?"));
        $this->assertEquals("asd&", $oUtils->appendParamSeparator("asd&"));
        $this->assertEquals("asd&amp;", $oUtils->appendParamSeparator("asd&amp;"));
        $this->assertEquals("asd&amp;a?", $oUtils->appendParamSeparator("asd&amp;a"));
        $this->assertEquals("asd?&amp;a&amp;", $oUtils->appendParamSeparator("asd?&amp;a"));
    }

    /**
     * Test cases for oxUtilsUrl::cleanUrlParams()
     * URL cleanup check, remove dublicate GET parameters and clean &amp; and dublicate &
     *
     * @return null
     */
    public function testCleanUrlParams()
    {
        $sTestUrl = oxRegistry::getConfig()->getConfigParam('sShopURL') . 'index.php?&&&p1=v1&p2=v2&aTest[]=test1&aTest[]=test2&assoc[test]=t1&assoc[test]=t2&amp;amp;amp;&&p1=test1 space&p2=';
        $sExpUrl = oxRegistry::getConfig()->getConfigParam('sShopURL') . 'index.php?p1=test1+space&amp;p2=&amp;aTest[0]=test1&amp;aTest[1]=test2&amp;assoc[test]=t2';

        $oUtils = oxRegistry::get("oxUtilsUrl");
        $this->assertSame($sExpUrl, $oUtils->cleanUrlParams($sTestUrl));
    }

    /**
     * Test cases for oxUtilsUrl::processSeoUrl()
     *
     * @return null
     */
    // admin - should stay plain seo url - no session ids, no security tokens and shop parameter
    // - current SHOP host url
    public function testProcessSeoUrlAdminCurrentShopHostUrl()
    {
        $sUrl = oxRegistry::getConfig()->getConfigParam("sShopURL") . "index.php?param1=value1";

        $oUtils = $this->getMock("oxUtilsUrl", array("isAdmin"));
        $oUtils->expects($this->any())->method('isAdmin')->will($this->returnValue(true));
        $this->assertEquals($sUrl, $oUtils->processSeoUrl($sUrl));
    }


    // non admin
    // - if needed - must be added shop id, session identifier etc..
    public function testProcessSeoUrlNonAdmin()
    {
        // base shop
        $iShopId = oxRegistry::getConfig()->getBaseShopId();
        modConfig::getInstance()->setShopId($iShopId);
        $sUrl = oxRegistry::getConfig()->getConfigParam("sShopURL");

        $oUtils = $this->getMock("oxUtilsUrl", array("isAdmin"));
        $oUtils->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $this->assertEquals($sUrl, $oUtils->processSeoUrl($sUrl));

    }

    public function testGetCurrentUrl_dataProvider()
    {
        $aData = array(
            array('', '', 'www.testshop.com', '', 'http://www.testshop.com'),
            array('', '', 'www.testshop.com:8080', '', 'http://www.testshop.com:8080'),
            array('', '', 'www.testshop.com', '/testFolder/index.php', 'http://www.testshop.com/testFolder/index.php'),
            array('', '', 'www.testshop.com', '/testFolder/index.php?lang=1', 'http://www.testshop.com/testFolder/index.php?lang=1'),
            array('1', '', 'www.testshop.com', '/testFolder/', 'https://www.testshop.com/testFolder/'),
            array('on', '', 'www.testshop.com', '/testFolder/index.php', 'https://www.testshop.com/testFolder/index.php'),
            array('', 'https', 'www.testshop.com', '/testFolder/index.php?lang=1', 'https://www.testshop.com/testFolder/index.php?lang=1'),
        );

        return $aData;
    }

    /**
     * oxUtilsUrl::getCurrentUrl() test case
     *
     * @dataProvider testGetCurrentUrl_dataProvider
     *
     * @return null
     */
    public function testGetCurrentUrl($sHttps, $sHttpXForwarded, $sHttpHost, $sRequestUri, $sResult)
    {
        $oUtils = new oxUtilsUrl();

        $oUtilsServer = $this->getMock('oxUtilsServer', array('getServerVar'));
        $oUtilsServer->expects($this->at(0))->method('getServerVar')->with($this->equalTo("HTTPS"))->will($this->returnValue($sHttps));
        $oUtilsServer->expects($this->at(1))->method('getServerVar')->with($this->equalTo("HTTP_X_FORWARDED_PROTO"))->will($this->returnValue($sHttpXForwarded));
        $oUtilsServer->expects($this->at(2))->method('getServerVar')->with($this->equalTo("HTTP_HOST"))->will($this->returnValue($sHttpHost));
        $oUtilsServer->expects($this->at(3))->method('getServerVar')->with($this->equalTo("REQUEST_URI"))->will($this->returnValue($sRequestUri));
        oxTestModules::addModuleObject('oxUtilsServer', $oUtilsServer);

        $this->assertEquals($sResult, $oUtils->getCurrentUrl());
    }

    public function providerIsCurrentShopHost()
    {
        $sShopUrl = $this->getConfig()->getShopUrl();

        return array(
            array('', true),
            array('relative-url', true),
            array($sShopUrl, true),
            array($sShopUrl . '?param=value', true),
            array('http://external-host.com', false),
            array('https://external-host.com', false),
            array('http://external-host.com?param=value', false),
        );
    }

    /**
     * @dataProvider providerIsCurrentShopHost
     */
    public function testIsCurrentShopHost($sUrl, $blResult)
    {
        $oUtils = new oxUtilsUrl();
        $this->assertSame($blResult, $oUtils->isCurrentShopHost($sUrl));
    }

    public function testIsCurrentShopHostWithMallShopURL()
    {
        return;

        $this->getConfig()->setConfigParam("sMallShopURL", 'http://shopHost');
        $this->getConfig()->setConfigParam("sShopURL", '');
        $this->getConfig()->setConfigParam("aLanguageURLs", array());

        $oUtils = new oxUtilsUrl();
        $this->assertSame(true, $oUtils->isCurrentShopHost('http://shopHost'));
    }

    public function testIsCurrentShopHostWithMallSslShopURL()
    {
        return;
        $this->getConfig()->setConfigParam("sMallShopURL", 'http://shopHost');
        $this->getConfig()->setConfigParam("sMallSSLShopURL", 'https://shopHost');
        $this->getConfig()->setConfigParam("sShopURL", '');
        $this->getConfig()->setConfigParam("aLanguageURLs", array());

        $oUtils = new oxUtilsUrl();
        $this->assertSame(true, $oUtils->isCurrentShopHost('https://shopHost'));
    }

    public function testIsCurrentShopHostWithShopURL()
    {
        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", 'http://shopHost');
        $this->getConfig()->setConfigParam("aLanguageURLs", array());

        $oUtils = new oxUtilsUrl();
        $this->assertSame(true, $oUtils->isCurrentShopHost('http://shopHost'));
    }

    public function testIsCurrentShopHostWithSslShopURL()
    {
        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", 'http://shopHost');
        $this->getConfig()->setConfigParam("sSSLShopURL", 'https://shopHost');
        $this->getConfig()->setConfigParam("aLanguageURLs", array());

        $oUtils = new oxUtilsUrl();
        $this->assertSame(true, $oUtils->isCurrentShopHost('https://shopHost'));
    }

    public function testIsCurrentShopHostWithLanguageURLs()
    {
        $this->setLanguage(1);

        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", '');
        $this->getConfig()->setConfigParam("aLanguageURLs", array(0 => 'http://german.shopHost', 1 => 'http://english.shopHost'));

        $oUtils = new oxUtilsUrl();
        $this->assertSame(true, $oUtils->isCurrentShopHost('http://english.shopHost'));
        $this->assertSame(false, $oUtils->isCurrentShopHost('http://german.shopHost'));
    }

    public function testIsCurrentShopHostWithSslLanguageURLs()
    {
        $this->setLanguage(1);

        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", '');
        $this->getConfig()->setConfigParam("aLanguageURLs", array(0 => 'http://german.shopHost', 1 => 'http://english.shopHost'));
        $this->getConfig()->setConfigParam("aLanguageSSLURLs", array(0 => 'https://german.shopHost.de', 1 => 'https://english.shopHost.en'));

        $oUtils = new oxUtilsUrl();
        $this->assertSame(true, $oUtils->isCurrentShopHost('https://english.shopHost.en'));
        $this->assertSame(false, $oUtils->isCurrentShopHost('https://german.shopHost.de'));
    }

    public function testIsCurrentShopHostWithSslAdminURL()
    {
        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", '');
        $this->getConfig()->setConfigParam("aLanguageURLs", array());
        $this->getConfig()->setConfigParam("sAdminSSLURL", 'https://adminHost');

        $oUtils = $this->getMock("oxUtilsUrl", array('isAdmin'));
        $oUtils->expects($this->once())->method('isAdmin')->will($this->returnValue(true));
        $this->assertSame(true, $oUtils->isCurrentShopHost('https://adminHost'));
    }

    /**
     * Test strings and return values
     *
     * @see testStringToParamsArray
     *
     * @return array
     */
    public function stringProvider()
    {
        return array(
            array("&a=b&c=2", array("a" => "b", "c" => 2)),
            array("&amp;a=b&c=2", array("a" => "b", "c" => 2)),
            array("&amp;a=bampc=2", array("a" => "bampc")),
            array("a=bc=2=4", array("a" => "bc")),
            array("a=b&c=2=4", array("a" => "b", "c" => 2)),
            array("&&&&a=b&c=2=4", array("a" => "b", "c" => 2)),
            array("", array()),);
    }

    /**
     * Checks that parameter string is parsed properly
     *
     * @dataProvider stringProvider
     */
    public function testStringToParamsArray($sString, $aExpected)
    {
        $oUtils = new oxUtilsUrl();
        $this->assertEquals($aExpected, $oUtils->stringToParamsArray($sString));
    }
}