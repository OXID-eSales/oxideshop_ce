<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxRegistry;
use \oxTestModules;

class UtilsUrlTest extends \PHPUnit\Framework\TestCase
{
    /**
     * oxUtilsUrl::prepareCanonicalUrl() test case
     */
    public function testPrepareCanonicalUrl()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return false;}');
        $this->getConfig()->setConfigParam("sDefaultLang", 9);
        $iLang = oxRegistry::getLang()->getBaseLanguage();

        $sExpUrl = "shop.com/index.php?param1=value1&amp;bonusid=111";
        $sExpUrl .= '&amp;lang=' . $iLang;

        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame($sExpUrl, $oUtils->prepareCanonicalUrl("shop.com/index.php?param1=value1&amp;bonusid=111&amp;sid=1234"));
    }

    /**
     * oxUtilsUrl::cleanUrl() test case
     */
    public function testCleanUrl()
    {
        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame("http://www.myoxideshop.com/index.php", $oUtils->cleanUrl("http://www.myoxideshop.com/index.php?param1=value1&param2=value2"));
        $this->assertSame("http://www.myoxideshop.com/index.php?param2=value2", $oUtils->cleanUrl("http://www.myoxideshop.com/index.php?param1=value1&param2=value2", ["param1"]));
    }

    public function testGetBaseAddUrlParamsPE()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame([], $oUtils->getBaseAddUrlParams());
    }

    public function testGetAddUrlParams()
    {
        $this->setRequestParameter("currency", 1);
        $aBaseUrlParams = ["param1" => "value1", "param2" => "value2"];

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, ["getBaseAddUrlParams"]);
        $oUtils->expects($this->once())->method('getBaseAddUrlParams')->willReturn($aBaseUrlParams);

        $aBaseUrlParams['cur'] = 1;
        $this->assertSame($aBaseUrlParams, $oUtils->getAddUrlParams());
    }

    public function testPrepareUrlForNoSessionSeoOn()
    {
        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return true;}');

        $this->assertSame('http://example.com/', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('http://example.com/?sid=abc123'));
        $this->assertSame('http://example.com/', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('http://example.com/?force_sid=abc123'));

        $this->assertSame('http://example.com/?cl=test', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('http://example.com/?cl=test&amp;sid=abc123'));
        $this->assertSame('http://example.com/?cl=test', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('http://example.com/?cl=test&amp;force_sid=abc123'));

        $this->assertSame('http://example.com/?cl=test', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('http://example.com/?sid=abc123&amp;cl=test'));
        $this->assertSame('http://example.com/?cl=test', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('http://example.com/?force_sid=abc123&amp;cl=test'));
    }

    public function testPrepareUrlForNoSession()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community/Professional edition only.');
        }

        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return false;}');
        oxTestModules::addFunction('oxLang', 'getBaseLanguage', '{return 3;}');

        $this->assertSame('sdf?lang=1', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?sid=111&lang=1'));
        $this->assertSame('sdf?a&lang=1', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?sid=111&a&lang=1'));
        $this->assertSame('sdf?a&amp;lang=1', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?sid=111&a&amp;lang=1'));
        $this->assertSame('sdf?a&&amp;lang=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?sid=111&a&'));
        $this->assertSame('sdf?lang=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf'));

        // even after setting some shop id, it must be working
        $this->getConfig()->setShopId(5);
        $this->assertSame('sdf?lang=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?sid=asd'));
        $this->assertSame('sdf?lang=2', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?sid=das&lang=2'));
        $this->assertSame('sdf?lang=2&shp=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?lang=2&sid=fs&amp;shp=3'));
        $this->assertSame('sdf?shp=2&amp;lang=2', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?shp=2&amp;lang=2'));
        $this->assertSame('sdf?shp=2&amp;lang=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?shp=2'));

        $this->assertSame('sdf?lang=1', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?force_sid=111&lang=1'));
        $this->assertSame('sdf?a&lang=1', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?force_sid=111&a&lang=1'));
        $this->assertSame('sdf?a&amp;lang=1', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?force_sid=111&a&amp;lang=1'));
        $this->assertSame('sdf?a&&amp;lang=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?force_sid=111&a&'));

        $this->assertSame('sdf?bonusid=111&amp;lang=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?bonusid=111'));
        $this->assertSame('sdf?a=1&bonusid=111&amp;lang=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?a=1&bonusid=111'));
        $this->assertSame('sdf?a=1&amp;bonusid=111&amp;lang=3', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf?a=1&amp;bonusid=111&amp;force_admin_sid=111'));

        $this->setRequestParameter('currency', 2);
        $this->assertSame('sdf?lang=3&amp;cur=2', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf'));

        oxTestModules::addFunction('oxUtils', 'seoIsActive', '{return true;}');
        $this->assertSame('sdf', \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->prepareUrlForNoSession('sdf'));
    }

    public function providerAppendUrl(): \Iterator
    {
        yield ['testUrl', [], 'testUrl?'];
        yield ['testUrl', ['p1' => 'v1', 'p2' => 'v2'], 'testUrl?p1=v1&amp;p2=v2&amp;'];
        yield ['testUrl?', [], 'testUrl?'];
        yield ['testUrl?', ['p1' => 'v1', 'p2' => 'v2'], 'testUrl?p1=v1&amp;p2=v2&amp;'];
        yield ['testUrl?p1=v1', ['p2' => 'v2'], 'testUrl?p1=v1&amp;p2=v2&amp;'];
        yield ['testUrl?p1=v1&amp;', ['p2' => 'v2'], 'testUrl?p1=v1&amp;p2=v2&amp;'];
        yield ['testUrl?p1=v1&amp;', [], 'testUrl?p1=v1&amp;'];
        yield ['testUrl?p1=v1&amp;', ['p1' => 'v1'], 'testUrl?p1=v1&amp;'];
        yield ['testUrl?p1=v1', ['p1' => null], 'testUrl?p1=v1&amp;'];
    }

    /**
     * @param string $sUrl
     * @param array  $aParams
     * @param string $sExpectedUrl
     *
     * @dataProvider providerAppendUrl
     */
    public function testAppendUrl($sUrl, $aParams, $sExpectedUrl)
    {
        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame($sExpectedUrl, $oUtils->appendUrl($sUrl, $aParams));
    }

    public function providerAppendUrlWithoutOverwriting(): \Iterator
    {
        yield ['testUrl?p1=v1', ['p1' => 'v11', 'p2' => 'v2'], 'testUrl?p1=v1&amp;p2=v2&amp;'];
    }

    /**
     * @param string $sUrl
     * @param array  $aParams
     * @param string $sExpectedUrl
     *
     * @dataProvider providerAppendUrlWithoutOverwriting
     */
    public function testAppendUrlWithoutOverwriting($sUrl, $aParams, $sExpectedUrl)
    {
        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame($sExpectedUrl, $oUtils->appendUrl($sUrl, $aParams, false));
    }

    public function providerAppendUrlWithFinalUrlForming(): \Iterator
    {
        yield ['testUrl', [], 'testUrl'];
        yield ['testUrl', ['p1' => 'v1', 'p2' => 'v2'], 'testUrl?p1=v1&amp;p2=v2'];
        yield ['testUrl?p1=v1', ['p2' => 'v2'], 'testUrl?p1=v1&amp;p2=v2'];
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
        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame($sExtectedUrl, $oUtils->appendUrl($sUrl, $aParams, true, true));
    }

    public function providerAddBaseUrl(): \Iterator
    {
        $sShopUrl = $this->getConfig()->getSslShopUrl();
        yield ['http://external-url', 'http://external-url'];
        yield ['local-url', $sShopUrl . 'local-url'];
        yield ['?param1=value=1', $sShopUrl . '?param1=value=1'];
        yield [$sShopUrl, $sShopUrl];
        yield [$sShopUrl . '?param1=value1', $sShopUrl . '?param1=value1'];
    }

    /**
     * @dataProvider providerAddBaseUrl
     */
    public function testAddBaseUrl($sUrl, $sExpectedUrl)
    {
        $oUtils = oxNew('oxUtilsUrl');

        $this->assertEquals($sExpectedUrl, $oUtils->addShopHost($sUrl));
    }

    public function testProcessUrlWithParametersAdded()
    {
        $oUtils = oxNew('oxUtilsUrl');

        $aParameters = ['param1' => 'value1', 'param2' => 'value2'];

        $sExpectedUrl = "http://some-url/?param1=value1&amp;param2=value2";
        $this->assertSame($sExpectedUrl, $oUtils->processUrl("http://some-url/", true, $aParameters));
    }

    public function testProcessUrlWithAdditionalParametersAddedToLocalUrl()
    {
        $aParameters = ['param1' => 'value1', 'param2' => 'value2'];

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, ['getBaseAddUrlParams']);
        $oUtils->method('getBaseAddUrlParams')->willReturn($aParameters);

        $sShopUrl = $this->getConfig()->getSslShopUrl();
        $sExpectedUrl = $sShopUrl . "?param1=value1&amp;param2=value2";
        $this->assertSame($sExpectedUrl, $oUtils->processUrl($sShopUrl));
    }

    public function testProcessUrlWithAdditionalParametersNotAddedToExternalUrl()
    {
        $aParameters = ['param1' => 'value1', 'param2' => 'value2'];

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, ['getBaseAddUrlParams']);
        $oUtils->method('getBaseAddUrlParams')->willReturn($aParameters);

        $this->assertSame("http://some-url/", $oUtils->processUrl("http://some-url/"));
    }

    public function testProcessUrlWithLocalUrlLanguageShouldBeAdded()
    {
        $this->getConfig()->setConfigParam('sDefaultLang', 0);
        $this->setLanguage(1);

        $oUtils = oxNew('oxUtilsUrl');

        $sShopUrl = $this->getConfig()->getShopUrl();
        $this->assertSame($sShopUrl . '/anyUrl?lang=1', $oUtils->processUrl($sShopUrl . '/anyUrl'));
    }

    public function testProcessUrlWithLocalUrlSIDShouldBeAdded()
    {
        /** @var oxSession $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSidNeeded']);
        $oSession->method('isSidNeeded')->willReturn(true);
        $oSession->setId('SID');

        $oUtils = oxNew('oxUtilsUrl');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);
        $sShopUrl = $this->getConfig()->getShopUrl();

        $this->assertSame($sShopUrl . '/anyUrl?force_sid=SID', $oUtils->processUrl($sShopUrl . '/anyUrl'));
    }

    public function testProcessUrlWithRelativeUrlShouldActLikeLocal()
    {
        /** @var oxSession $oSession */
        $oSession = $this->getMock(\OxidEsales\Eshop\Core\Session::class, ['isSidNeeded']);
        $oSession->method('isSidNeeded')->willReturn(true);
        $oSession->setId('SID');

        $this->getConfig()->setConfigParam('sDefaultLang', 0);
        $this->setLanguage(1);

        $oUtils = oxNew('oxUtilsUrl');
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Session::class, $oSession);

        $this->assertSame("anyUrl?lang=1&amp;force_sid=SID", $oUtils->processUrl("anyUrl"));
    }

    public function testProcessUrlWithExternalUrlNoLanguageShouldBeAdded()
    {
        $this->getConfig()->setConfigParam('sDefaultLang', 0);
        $this->setLanguage(1);

        $oUtils = oxNew('oxUtilsUrl');

        $this->assertSame("http://www.external-url.com/anyUrl", $oUtils->processUrl("http://www.external-url.com/anyUrl"));
    }

    public function testProcessUrlWithExternalUrlNoSIDShouldBeAdded()
    {
        $this->getSession()->setVariable('blSidNeeded', true);
        $this->getSession()->setId('SID');

        $oUtils = oxNew('oxUtilsUrl');

        $this->assertSame("http://www.external-url.com/anyUrl", $oUtils->processUrl("http://www.external-url.com/anyUrl"));
    }

    public function testProcessUrlWithNonFinalUrl()
    {
        $this->getConfig()->setConfigParam('sDefaultLang', 0);
        $this->setLanguage(1);

        $oUtils = oxNew('oxUtilsUrl');

        $this->assertSame("anyUrl?param=val1&amp;lang=1&amp;", $oUtils->processUrl("anyUrl", false, ['param' => 'val1']));
    }

    public function testAppendParamSeparator()
    {
        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame("asd?", $oUtils->appendParamSeparator("asd"));
        $this->assertSame("asd?", $oUtils->appendParamSeparator("asd?"));
        $this->assertSame("asd&", $oUtils->appendParamSeparator("asd&"));
        $this->assertSame("asd&amp;", $oUtils->appendParamSeparator("asd&amp;"));
        $this->assertSame("asd&amp;a?", $oUtils->appendParamSeparator("asd&amp;a"));
        $this->assertSame("asd?&amp;a&amp;", $oUtils->appendParamSeparator("asd?&amp;a"));
    }

    /**
     * Test cases for oxUtilsUrl::cleanUrlParams()
     * URL cleanup check, remove duplicate GET parameters and clean &amp; and duplicate &
     */
    public function testCleanUrlParams()
    {
        $sTestUrl = $this->getConfig()->getConfigParam('sShopURL') . 'index.php?&&&p1=v1&p2=v2&aTest[]=test1&aTest[]=test2&assoc[test]=t1&assoc[test]=t2&amp;amp;amp;&&p1=test1 space&p2=';
        $sExpUrl = $this->getConfig()->getConfigParam('sShopURL') . 'index.php?p1=test1+space&amp;p2=&amp;aTest[0]=test1&amp;aTest[1]=test2&amp;assoc[test]=t2';

        $oUtils = \OxidEsales\Eshop\Core\Registry::getUtilsUrl();
        $this->assertSame($sExpUrl, $oUtils->cleanUrlParams($sTestUrl));
    }

    /**
     * Test cases for oxUtilsUrl::processSeoUrl()
     */
    // admin - should stay plain seo url - no session ids, no security tokens and shop parameter
    // - current SHOP host url
    public function testProcessSeoUrlAdminCurrentShopHostUrl()
    {
        $sUrl = $this->getConfig()->getConfigParam("sShopURL") . "index.php?param1=value1";

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, ["isAdmin"]);
        $oUtils->method('isAdmin')->willReturn(true);
        $this->assertSame($sUrl, $oUtils->processSeoUrl($sUrl));
    }

    // non admin
    // - if needed - must be added shop id, session identifier etc..
    public function testProcessSeoUrlNonAdmin()
    {
        // base shop
        $iShopId = $this->getConfig()->getBaseShopId();
        $this->getConfig()->setShopId($iShopId);
        $sUrl = $this->getConfig()->getConfigParam("sShopURL");

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, ["isAdmin"]);
        $oUtils->method('isAdmin')->willReturn(false);
        $this->assertEquals($sUrl, $oUtils->processSeoUrl($sUrl));
    }

    public function testGetCurrentUrl_dataProvider(): \Iterator
    {
        yield ['', '', 'www.testshop.com', '', 'http://www.testshop.com'];
        yield ['', '', 'www.testshop.com:8080', '', 'http://www.testshop.com:8080'];
        yield ['', '', 'www.testshop.com', '/testFolder/index.php', 'http://www.testshop.com/testFolder/index.php'];
        yield ['', '', 'www.testshop.com', '/testFolder/index.php?lang=1', 'http://www.testshop.com/testFolder/index.php?lang=1'];
        yield ['1', '', 'www.testshop.com', '/testFolder/', 'https://www.testshop.com/testFolder/'];
        yield ['on', '', 'www.testshop.com', '/testFolder/index.php', 'https://www.testshop.com/testFolder/index.php'];
        yield ['', 'https', 'www.testshop.com', '/testFolder/index.php?lang=1', 'https://www.testshop.com/testFolder/index.php?lang=1'];
    }

    /**
     * oxUtilsUrl::getCurrentUrl() test case
     *
     * @dataProvider testGetCurrentUrl_dataProvider
     */
    public function testGetCurrentUrl($sHttps, $sHttpXForwarded, $sHttpHost, $sRequestUri, $sResult)
    {
        $oUtils = oxNew('oxUtilsUrl');

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ['getServerVar']);
        $oUtilsServer
            ->method('getServerVar')
            ->withConsecutive(['HTTPS'], ['HTTP_X_FORWARDED_PROTO'], ['HTTP_HOST'], ['REQUEST_URI'])
            ->willReturnOnConsecutiveCalls(
                $sHttps,
                $sHttpXForwarded,
                $sHttpHost,
                $sRequestUri
            );

        oxTestModules::addModuleObject('oxUtilsServer', $oUtilsServer);

        $this->assertEquals($sResult, $oUtils->getCurrentUrl());
    }

    public function providerIsCurrentShopHost(): \Iterator
    {
        $sShopUrl = $this->getConfig()->getShopUrl();
        yield ['', true];
        yield ['relative-url', true];
        yield [$sShopUrl, true];
        yield [$sShopUrl . '?param=value', true];
        yield ['http://external-host.com', false];
        yield ['https://external-host.com', false];
        yield ['http://external-host.com?param=value', false];
    }

    /**
     * @dataProvider providerIsCurrentShopHost
     */
    public function testIsCurrentShopHost($sUrl, $blResult)
    {
        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame($blResult, $oUtils->isCurrentShopHost($sUrl));
    }

    public function testIsCurrentShopHostWithShopURL()
    {
        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", 'http://shopHost');
        $this->getConfig()->setConfigParam("aLanguageURLs", []);

        $oUtils = oxNew('oxUtilsUrl');
        $this->assertTrue($oUtils->isCurrentShopHost('http://shopHost'));
    }

    public function testIsCurrentShopHostWithSslShopURL()
    {
        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", 'http://shopHost');
        $this->getConfig()->setConfigParam("sSSLShopURL", 'https://shopHost');
        $this->getConfig()->setConfigParam("aLanguageURLs", []);

        $oUtils = oxNew('oxUtilsUrl');
        $this->assertTrue($oUtils->isCurrentShopHost('https://shopHost'));
    }

    public function testIsCurrentShopHostWithLanguageURLs()
    {
        $this->setLanguage(1);

        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", '');
        $this->getConfig()->setConfigParam("aLanguageURLs", [0 => 'http://german.shopHost', 1 => 'http://english.shopHost']);

        $oUtils = oxNew('oxUtilsUrl');
        $this->assertTrue($oUtils->isCurrentShopHost('http://english.shopHost'));
        $this->assertFalse($oUtils->isCurrentShopHost('http://german.shopHost'));
    }

    public function testIsCurrentShopHostWithSslLanguageURLs()
    {
        $this->setLanguage(1);

        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", '');
        $this->getConfig()->setConfigParam("aLanguageURLs", [0 => 'http://german.shopHost', 1 => 'http://english.shopHost']);
        $this->getConfig()->setConfigParam("aLanguageSSLURLs", [0 => 'https://german.shopHost.de', 1 => 'https://english.shopHost.en']);

        $oUtils = oxNew('oxUtilsUrl');
        $this->assertTrue($oUtils->isCurrentShopHost('https://english.shopHost.en'));
        $this->assertFalse($oUtils->isCurrentShopHost('https://german.shopHost.de'));
    }

    public function testIsCurrentShopHostWithSslAdminURL()
    {
        $this->getConfig()->setConfigParam("sMallShopURL", '');
        $this->getConfig()->setConfigParam("sShopURL", '');
        $this->getConfig()->setConfigParam("aLanguageURLs", []);
        $this->getConfig()->setConfigParam("sAdminSSLURL", 'https://adminHost');

        $oUtils = $this->getMock(\OxidEsales\Eshop\Core\UtilsUrl::class, ['isAdmin']);
        $oUtils->expects($this->once())->method('isAdmin')->willReturn(true);
        $this->assertTrue($oUtils->isCurrentShopHost('https://adminHost'));
    }

    /**
     * Test strings and return values
     *
     * @see testStringToParamsArray
     *
     * @return array
     */
    public function stringProvider(): \Iterator
    {
        yield ["&a=b&c=2", ["a" => "b", "c" => 2]];
        yield ["&amp;a=b&c=2", ["a" => "b", "c" => 2]];
        yield ["&amp;a=bampc=2", ["a" => "bampc"]];
        yield ["a=bc=2=4", ["a" => "bc"]];
        yield ["a=b&c=2=4", ["a" => "b", "c" => 2]];
        yield ["&&&&a=b&c=2=4", ["a" => "b", "c" => 2]];
        yield ["", []];
    }

    /**
     * Checks that parameter string is parsed properly
     *
     * @dataProvider stringProvider
     */
    public function testStringToParamsArray($sString, $aExpected)
    {
        $oUtils = oxNew('oxUtilsUrl');
        $this->assertEquals($aExpected, $oUtils->stringToParamsArray($sString));
    }

    public function providerGetsHostFromUrl(): \Iterator
    {
        yield ['testHost', 'testHost'];
        yield ['testHost.de', 'testHost.de'];
        yield ['testHost.de:8061', 'testHost.de'];
        yield ['testHost.de/subdirectory', 'testHost.de'];
        yield ['testHost.de:8061/subdirectory', 'testHost.de'];
        yield ['www.testHost.de', 'www.testHost.de'];
        yield ['www.testHost.de:8061', 'www.testHost.de'];
        yield ['http://www.testHost.de:8061', 'www.testHost.de'];
        yield ['https://www.testHost.de:8061', 'www.testHost.de'];
        yield ['https://127.0.0.1:8061', '127.0.0.1'];
        yield ['https://www.testHost.de/sudirectory/', 'www.testHost.de'];
        yield ['https://www.testHost.de:8061/sudirectory/', 'www.testHost.de'];
        yield ['127.0.0.1', '127.0.0.1'];
        yield ['https://127.0.0.1:8061', '127.0.0.1'];
    }

    /**
     * @param string $url
     * @param string $host
     *
     * @dataProvider providerGetsHostFromUrl
     */
    public function testGetsActiveShopHost($url, $host)
    {
        $oConfig = $this->getMock('oxConfig');
        $oConfig->method('getShopUrl')->willReturn($url);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame($host, $oUtils->getActiveShopHost());
    }

    /**
     * @return array
     */
    public function providerGetsActiveShopUrlPath(): \Iterator
    {
        yield ['http://test-oxid-shop.com/subdirectory/other_shop', '/subdirectory/other_shop'];
        yield ['http://test-oxid-shop.com:6425/subdirectory/other_shop', '/subdirectory/other_shop'];
        yield ['https://127.0.0.1:6425/subdirectory/other_shop', '/subdirectory/other_shop'];
        yield ['https://127.0.0.1:6425', null];
        yield ['example.com/subdirectory/other_shop', '/subdirectory/other_shop'];
        yield ['wrong url', null];
    }

    /**
     * @param string $url
     * @param string|null $result
     *
     * @dataProvider providerGetsActiveShopUrlPath
     */
    public function testExtractsUrlPath($url, $result)
    {
        $utilsUrl = oxNew('oxUtilsUrl');

        $this->assertSame($result, $utilsUrl->extractUrlPath($url));
    }

    /**
     * @param string $url
     * @param string|null $result
     *
     * @dataProvider providerGetsActiveShopUrlPath
     */
    public function testGetsActiveShopUrlPath($url, $result)
    {
        $oConfig = $this->getMock('oxConfig');
        $oConfig->method('getShopUrl')->willReturn($url);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $utilsUrl = oxNew('oxUtilsUrl');

        $this->assertSame($result, $utilsUrl->getActiveShopUrlPath());
    }

    /**
     * @param string $url
     * @param string $host
     *
     * @dataProvider providerGetsHostFromUrl
     */
    public function testExtractsHostFromUrl($url, $host)
    {
        $oUtils = oxNew('oxUtilsUrl');
        $this->assertSame($host, $oUtils->extractHost($url));
    }

    public function providerGetUrlLanguageParameter(): \Iterator
    {
        yield [0, ['lang' => 0]];
        yield [1, ['lang' => 1]];
    }

    /**
     * @param int $languageId
     * @param array $expectedLanguageUrlParameter
     *
     * @dataProvider providerGetUrlLanguageParameter
     */
    public function testGetUrlLanguageParameter($languageId, $expectedLanguageUrlParameter)
    {
        oxRegistry::getLang()->setBaseLanguage(0);

        $utilsUrl = oxNew('oxUtilsUrl');
        $this->assertSame($expectedLanguageUrlParameter, $utilsUrl->getUrlLanguageParameter($languageId));
    }
}
