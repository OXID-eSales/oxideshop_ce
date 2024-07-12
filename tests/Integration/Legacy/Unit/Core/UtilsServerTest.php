<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxRegistry;

class UtilsServerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown(): void
    {
        $this->getConfig()->setConfigParam("aTrustedIPs", []);
        parent::tearDown();
    }

    /**
     * oxUtilsServer::setOxCookie() test case
     */
    public function testSetOxCookieForSaveSessionCookie()
    {
        $sValue = 'some value';
        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ["saveSessionCookie"]);
        // One cookie will be saved to session another will not.
        $oUtilsServer->expects($this->once())->method('saveSessionCookie');
        $oUtilsServer->setOxCookie("testName1", $sValue);
        // Check if do not save to session when pass false(sixth param) as not to save to session.
        $oUtilsServer->setOxCookie("testName2", $sValue, 0, '/', null, false);
    }

    /**
     * oxUtilsServer::_mustSaveToSession() test case
     */
    public function testMustSaveToSessionNoSslUrl()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getSslShopUrl"]);
        $oConfig->expects($this->once())->method('getSslShopUrl')->willReturn(false);

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $this->assertFalse($oUtilsServer->mustSaveToSession());
    }

    /**
     * oxUtilsServer::_mustSaveToSession() test case
     */
    public function testMustSaveToSession()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["getSslShopUrl", "getShopUrl"]);
        $oConfig->expects($this->once())->method('getSslShopUrl')->willReturn("https://ssl.oxid.com");
        $oConfig->expects($this->once())->method('getShopUrl')->willReturn("http://www.oxid.com");

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $this->assertFalse($oUtilsServer->mustSaveToSession());
    }

    /**
     * oxUtilsServer::_mustSaveToSession() test case
     */
    public function testGetSessionCookieKey()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ["isSsl"]);
        $oConfig
            ->method('isSsl')
            ->willReturnOnConsecutiveCalls(
                true,
                false,
                true,
                false
            );

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ["getConfig"]);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $this->assertSame('ssl', $oUtilsServer->getSessionCookieKey(true));
        $this->assertSame('nossl', $oUtilsServer->getSessionCookieKey(true));
        $this->assertSame('nossl', $oUtilsServer->getSessionCookieKey(false));
        $this->assertSame('ssl', $oUtilsServer->getSessionCookieKey(false));
    }

    /**
     * oxUtilsServer::_saveSessionCookie() test case
     */
    public function testSaveSessionCookie()
    {
        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ["mustSaveToSession", "getSessionCookieKey"]);
        $oUtilsServer->method('mustSaveToSession')->willReturn(true);
        $oUtilsServer->method('getSessionCookieKey')->willReturn("key");
        $oUtilsServer->saveSessionCookie("var1", "val1", 123, "path1", "domain1");
        $oUtilsServer->saveSessionCookie("var2", "val2", 321, "path2", "domain2");

        $aVal = ["key" => ["var1" => ["value" => "val1", "expire" => 123, "path" => "path1", "domain" => "domain1"], "var2" => ["value" => "val2", "expire" => 321, "path" => "path2", "domain" => "domain2"]]];
        $this->assertSame($aVal, $this->getSession()->getVariable("aSessionCookies"));
    }

    /**
     * oxUtilsServer::loadSessionCookies() test case
     */
    public function testLoadSessionCookies()
    {
        $aVal = ["key" => ["var1" => ["value" => "val1", "expire" => 123, "path" => "path1", "domain" => "domain1"], "var2" => ["value" => "val2", "expire" => 321, "path" => "path2", "domain" => "domain2"]]];

        $this->getSession()->setVariable('aSessionCookies', $aVal);

        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ["getSessionCookieKey", "setOxCookie"]);
        $oUtilsServer->method('getSessionCookieKey')->willReturn("key");
        $oUtilsServer->loadSessionCookies();

        $this->assertSame([], oxRegistry::getSession()->getVariable('aSessionCookies'));
    }

    public function testIsTrustedClientIp()
    {
        $oUtilsServer = oxNew('oxUtilsServer');
        $this->assertFalse($oUtilsServer->isTrustedClientIp());

        //
        $this->getConfig()->setConfigParam("aTrustedIPs", ["xxx"]);
        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, ["getRemoteAddress"]);
        $oUtilsServer->expects($this->once())->method('getRemoteAddress')->willReturn("xxx");
        $this->assertTrue($oUtilsServer->isTrustedClientIp());
    }

    public function testGetCookiePathWhenACookiePathsIssetup(): void
    {
        $sShopId = $this->getConfig()->getShopId();
        $this->getConfig()->setConfigParam("aCookiePaths", [$sShopId => 'somepath']);

        $oUtilsServer = oxNew('oxUtilsServer');
        $this->assertSame('somepath', $oUtilsServer->getCookiePath(""));
    }

    public function testGetCookieDomainWhenACookieDomainsIssetup(): void
    {
        $sShopId = $this->getConfig()->getShopId();
        $this->getConfig()->setConfigParam("aCookieDomains", [$sShopId => 'somedomain']);

        $oUtilsServer = oxNew('oxUtilsServer');
        $this->assertSame('somedomain', $oUtilsServer->getCookieDomain(""));
    }

    public function testGetCookiePath()
    {
        $oUtilsServer = oxNew('oxUtilsServer');
        $this->assertSame("xxx", $oUtilsServer->getCookiePath("xxx"));
        $this->assertSame("", $oUtilsServer->getCookiePath(null));
    }

    /**
     * test is actually nonsense under unit testing
     * Reason: The testant immediately and explicitly returns on defined('OXID_PHP_UNIT')
     */
    public function testSetCookie()
    {
        $sName = "someName";
        $sValue = "someValue";
        $this->assertNull(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->setOxCookie($sName, $sValue));
    }

    public function testGetCookie()
    {
        $aC = $_COOKIE;
        try {
            $_COOKIE['test'] = "asd'\"\000aa";
            $this->assertSame("asd&#039;&quot;aa", \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie('test'));
        } catch (Exception $exception) {
        }

        // restore data
        $_COOKIE = $aC;

        // check if exception has beed thrown
        if ($exception) {
            throw $exception;
        }
    }

    public function testGetRemoteAddress()
    {
        $sIP = '127.0.0.1';
        // in test mode, there are no remote adresses, thus null
        unset($_SERVER["HTTP_X_FORWARDED_FOR"]);
        unset($_SERVER["HTTP_CLIENT_IP"]);
        if (isset($_SERVER["REMOTE_ADDR"])) {
            $this->assertNull(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getRemoteAddress());
        } else {
            $_SERVER["REMOTE_ADDR"] = $sIP;
            $this->assertSame(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getRemoteAddress(), $sIP);
        }

        $_SERVER["HTTP_X_FORWARDED_FOR"] = $sIP;
        $this->assertSame(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getRemoteAddress(), $sIP);
        unset($_SERVER["HTTP_X_FORWARDED_FOR"]);
        $_SERVER["HTTP_CLIENT_IP"] = $sIP;
        $this->assertSame(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getRemoteAddress(), $sIP);
        unset($_SERVER["HTTP_CLIENT_IP"]);
    }

    public function testGetRemoteAddressProxyUsage()
    {
        $sIP = '127.0.0.1';
        $sProxy = '127.5.4.4';
        // in test mode, there are no remote adresses, thus null
        $_SERVER["HTTP_X_FORWARDED_FOR"] = $sIP . ',' . $sProxy;
        $this->assertSame(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getRemoteAddress(), $sIP);
        unset($_SERVER["HTTP_X_FORWARDED_FOR"]);
    }

    public function testGetServerVar()
    {
        $sName = md5(uniqid());
        $sValue = time();

        $_SERVER[$sName] = $sValue;
        ;
        $this->assertSame($sValue, \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getServerVar($sName));
        $this->assertEquals($_SERVER, \OxidEsales\Eshop\Core\Registry::getUtilsServer()->getServerVar());
    }

    /**
     * oxUtilsServer::processUserAgentInfo test case
     */
    public function testProcessUserAgentInfo()
    {
        $aServerInfo = ["Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)"                                           =>
            "Mozilla/4.0 (compatible; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)", "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8 ( .NET CLR 3.5.30729; .NET4.0C)"                                                                               =>
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8 ( .NET CLR 3.5.30729; .NET4.0C)", "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)"                                                                        =>
            "Mozilla/4.0 (compatible; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)", "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729)"                                                                                          =>
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729)", "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)"                               =>
            "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)", "Mozilla/4.0 (compatible; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)"                                         =>
            "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)", "Mozilla/5.0 (Windows; U; Windows NT 6.1; lt; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8"                                                                                                                  =>
            "Mozilla/5.0 (Windows; U; Windows NT 6.1; lt; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8", "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8"                                                                                                               =>
            "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8", "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; Creative AutoUpdate v1.40.01)" =>
            "Mozilla/4.0 (compatible; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; Creative AutoUpdate v1.40.01)", "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; GTB6.5; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)"                                                                =>
            "Mozilla/4.0 (compatible; Windows NT 5.1; Trident/4.0; GTB6.5; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)", "Mozilla/5.0 (Windows; U; Windows NT 5.1; lt; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2 ( .NET CLR 3.5.30729)"                                                                                            =>
            "Mozilla/5.0 (Windows; U; Windows NT 5.1; lt; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2 ( .NET CLR 3.5.30729)", "Opera/9.80 (Windows NT 5.1; U; en) Presto/2.6.30 Version/10.60"                                                                                                                                         =>
            "Opera/9.80 (Windows NT 5.1; U; en) Presto/2.6.30 Version/10.60", "Mozilla/4.0 (compatible; MSIE 8.0; AOL 9.1; AOLBuild 4334.34; Windows NT 5.1; SV1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; .NET CLR 1.1.4322)"                                        =>
            "Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.1; AOLBuild 4334.34; Windows NT 5.1; SV1; Mozilla/4.0 (compatible; MSIE 5.0; Windows NT 5.1; SV1) ; .NET CLR 1.1.4322)"];

        $oUtils = oxNew('oxUtilsServer');
        foreach ($aServerInfo as $sKey => $sVal) {
            $this->assertEquals($oUtils->processUserAgentInfo($sVal), $oUtils->processUserAgentInfo($sKey));
        }
    }

    public function testGetServerIp()
    {
        $sOldServerIp = $_SERVER['SERVER_ADDR'];

        $sExpectedIP = '192.168.0.9';
        $_SERVER['SERVER_ADDR'] = $sExpectedIP;
        $oUtils = oxNew('oxUtilsServer');
        $sServerIp = $oUtils->getServerIp();

        $_SERVER['SERVER_ADDR'] = $sOldServerIp;

        $this->assertSame($sExpectedIP, $sServerIp);
    }

    public function testGetServerNodeIdNotEmpty()
    {
        $oUtilsServer = oxNew('oxUtilsServer');
        $sServerId = $oUtilsServer->getServerNodeId();

        $this->assertNotEmpty($sServerId);

        return $sServerId;
    }

    #[\PHPUnit\Framework\Attributes\Depends('testGetServerNodeIdNotEmpty')]
    public function testGetServerNodeIdReturnSameValue($sServerId1)
    {
        $oUtilsServer = oxNew('oxUtilsServer');
        $sServerId2 = $oUtilsServer->getServerNodeId();

        $this->assertSame($sServerId1, $sServerId2);
    }

    #[\PHPUnit\Framework\Attributes\Depends('testGetServerNodeIdNotEmpty')]
    public function testGetServerNodeIdReturnDifferentValueIfDifferentIp($sServerId1)
    {
        $sOldServerIp = $_SERVER['SERVER_ADDR'];

        $sExpectedIP = '1.168.0.9';
        $_SERVER['SERVER_ADDR'] = $sExpectedIP;

        $oUtilsServer = oxNew('oxUtilsServer');
        $sServerId2 = $oUtilsServer->getServerNodeId();

        $_SERVER['SERVER_ADDR'] = $sOldServerIp;

        $this->assertNotSame($sServerId1, $sServerId2);
    }

    /**
     * @return array
     */
    public function providerIsCurrentWithSameHost(): \Iterator
    {
        yield ['/index.php', 'http://oxideshop.dev/index.php', true];
        yield ['/shop1/index.php', 'http://oxideshop.dev/shop1/index.php', true];
        yield ['/modules/oe/test_module/module_index.php', 'http://oxideshop.dev/module_index.php', true];
        yield ['/modules/test_module/module_index.php', 'http://oxideshop.dev/module_index.php', true];
        yield ['/shop1/modules/test_module/module_index.php', 'http://oxideshop.dev/shop1/module_index.php', true];
        yield ['/shop1/index.php', 'http://oxideshop.dev/shop2/index.php', false];
        yield ['/shop1/modules/test_module/module_index.php', 'http://oxideshop.dev/shop2/module_index.php', false];
    }

    /**
     * @param string $scriptName
     * @param string $url
     * @param bool   $result
     *
     * @dataProvider providerIsCurrentWithSameHost
     */
    public function testIsCurrentUrlWithSameHost($scriptName, $url, $result)
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'oxideshop.dev';
        $_SERVER['SCRIPT_NAME'] = $scriptName;
        $sUrl = $url;

        $this->assertSame($result, $oConfig->isCurrentUrl($sUrl));
    }

    /**
     * Testing URL checker
     *
     * by passing empty URL it returns false
     */
    public function testIsCurrentUrlNoUrl()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertTrue($oConfig->isCurrentUrl(''));
    }

    public function testIsCurrentUrlRandomUrl()
    {
        $sUrl = 'http://www.example.com/example/example.php';
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $this->assertFalse($oConfig->isCurrentUrl($sUrl));
    }

    public function testIsCurrentUrlPassingCurrent()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sUrl = $oConfig->getConfigParam('sShopURL') . '/example.php';
        $this->assertFalse($oConfig->isCurrentUrl($sUrl));
    }

    public function testIsCurrentUrlNoProtocol()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sUrl = 'www.example.com';
        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }

    public function testIsCurrentUrlBadProtocol()
    {
        $oConfig = oxNew('oxConfig');
        $oConfig->init();

        $sUrl = 'ftp://www.example.com';
        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }

    public function testIsCurrentUrlBugFixTest()
    {
        $sUrl = 'http://www.example.com.ru';
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'http://www.example.com';
        $_SERVER['SCRIPT_NAME'] = '';
        $this->assertfalse($oConfig->isCurrentUrl($sUrl));

        $sUrl = 'http://www.example.com';
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'http://www.example.com.ru';
        $_SERVER['SCRIPT_NAME'] = '';
        $this->assertfalse($oConfig->isCurrentUrl($sUrl));

        //#4010: force_sid added in https to every link
        $sUrl = 'https://www.example.com.ru';
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'www.example.com.ru';
        $_SERVER['SCRIPT_NAME'] = '';
        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }

    /**
     * Bug fix 0005685: Varnish issues on balanced system
     * Force sid is added on each link if proxy is in between client and Shop server.
     */
    public function testIsCurrentUrlWithLoadBalancer()
    {
        $sUrl = 'https://www.example.com.ru';
        $oConfig = oxNew('oxConfig');
        $oConfig->init();
        $_SERVER['HTTP_HOST'] = 'www.loadbalancer.de';
        $_SERVER['SCRIPT_NAME'] = '';
        $_SERVER['HTTP_X_FORWARDED_HOST'] = 'www.example.com.ru';
        $this->assertTrue($oConfig->isCurrentUrl($sUrl));
    }
}
