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

class Unit_Core_oxUtilsServerTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRegistry::getConfig()->setConfigParam("aTrustedIPs", array());
        parent::tearDown();
    }

    /**
     * oxUtilsServer::setOxCookie() test case
     *
     * @return null
     */
    public function testSetOxCookieForSaveSessionCookie()
    {
        $sValue = 'some value';
        $oUtilsServer = $this->getMock("oxUtilsServer", array("_saveSessionCookie"));
        // One cookie will be saved to session another will not.
        $oUtilsServer->expects($this->once())->method('_saveSessionCookie');
        $oUtilsServer->setOxCookie("testName1", $sValue);
        // Check if do not save to session when pass false(sixth param) as not to save to session.
        $oUtilsServer->setOxCookie("testName2", $sValue, 0, '/', null, false);
    }

    /**
     * oxUtilsServer::_mustSaveToSession() test case
     *
     * @return null
     */
    public function testMustSaveToSessionNoSslUrl()
    {
        $oConfig = $this->getMock("oxConfig", array("getSslShopUrl"));
        $oConfig->expects($this->once())->method('getSslShopUrl')->will($this->returnValue(false));

        $oUtilsServer = $this->getMock("oxUtilsServer", array("getConfig"));
        $oUtilsServer->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertFalse($oUtilsServer->UNITmustSaveToSession());
    }

    /**
     * oxUtilsServer::_mustSaveToSession() test case
     *
     * @return null
     */
    public function testMustSaveToSession()
    {
        $oConfig = $this->getMock("oxConfig", array("getSslShopUrl", "getShopUrl"));
        $oConfig->expects($this->once())->method('getSslShopUrl')->will($this->returnValue("https://ssl.oxid.com"));
        $oConfig->expects($this->once())->method('getShopUrl')->will($this->returnValue("http://www.oxid.com"));

        $oUtilsServer = $this->getMock("oxUtilsServer", array("getConfig"));
        $oUtilsServer->expects($this->once())->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertFalse($oUtilsServer->UNITmustSaveToSession());
    }

    /**
     * oxUtilsServer::_mustSaveToSession() test case
     *
     * @return null
     */
    public function testGetSessionCookieKey()
    {
        $oConfig = $this->getMock("oxConfig", array("isSsl"));
        $oConfig->expects($this->at(0))->method('isSsl')->will($this->returnValue(true));
        $oConfig->expects($this->at(1))->method('isSsl')->will($this->returnValue(false));
        $oConfig->expects($this->at(2))->method('isSsl')->will($this->returnValue(true));
        $oConfig->expects($this->at(3))->method('isSsl')->will($this->returnValue(false));

        $oUtilsServer = $this->getMock("oxUtilsServer", array("getConfig"));
        $oUtilsServer->expects($this->exactly(4))->method('getConfig')->will($this->returnValue($oConfig));
        $this->assertEquals('ssl', $oUtilsServer->UNITgetSessionCookieKey(true));
        $this->assertEquals('nossl', $oUtilsServer->UNITgetSessionCookieKey(true));
        $this->assertEquals('nossl', $oUtilsServer->UNITgetSessionCookieKey(false));
        $this->assertEquals('ssl', $oUtilsServer->UNITgetSessionCookieKey(false));
    }

    /**
     * oxUtilsServer::_saveSessionCookie() test case
     *
     * @return null
     */
    public function testSaveSessionCookie()
    {
        $oUtilsServer = $this->getMock("oxUtilsServer", array("_mustSaveToSession", "_getSessionCookieKey"));
        $oUtilsServer->expects($this->any())->method('_mustSaveToSession')->will($this->returnValue(true));
        $oUtilsServer->expects($this->any())->method('_getSessionCookieKey')->will($this->returnValue("key"));
        $oUtilsServer->UNITsaveSessionCookie("var1", "val1", 123, "path1", "domain1");
        $oUtilsServer->UNITsaveSessionCookie("var2", "val2", 321, "path2", "domain2");

        $aVal = array("key" => array("var1" => array("value" => "val1", "expire" => 123, "path" => "path1", "domain" => "domain1"),
                                     "var2" => array("value" => "val2", "expire" => 321, "path" => "path2", "domain" => "domain2")));
        $this->assertEquals($aVal, modSession::getInstance()->getVar("aSessionCookies"));
    }

    /**
     * oxUtilsServer::loadSessionCookies() test case
     *
     * @return null
     */
    public function testLoadSessionCookies()
    {
        $aVal = array("key" => array("var1" => array("value" => "val1", "expire" => 123, "path" => "path1", "domain" => "domain1"),
                                     "var2" => array("value" => "val2", "expire" => 321, "path" => "path2", "domain" => "domain2")));

        modSession::getInstance()->setVar('aSessionCookies', $aVal);

        $oUtilsServer = $this->getMock("oxUtilsServer", array("_getSessionCookieKey", "setOxCookie"));
        $oUtilsServer->expects($this->at(0))->method('_getSessionCookieKey')->will($this->returnValue("key"));
        $oUtilsServer->expects($this->at(1))->method('setOxCookie')->with($this->equalTo("var1"), $this->equalTo("val1"), $this->equalTo(123), $this->equalTo("path1"), $this->equalTo("domain1"), $this->equalTo(false));
        $oUtilsServer->expects($this->at(2))->method('setOxCookie')->with($this->equalTo("var2"), $this->equalTo("val2"), $this->equalTo(321), $this->equalTo("path2"), $this->equalTo("domain2"), $this->equalTo(false));
        $oUtilsServer->loadSessionCookies();

        $this->assertEquals(array(), oxRegistry::getSession()->getVariable('aSessionCookies'));
    }

    public function testIsTrustedClientIp()
    {
        $oUtilsServer = new oxUtilsServer();
        $this->assertFalse($oUtilsServer->isTrustedClientIp());

        //
        oxRegistry::getConfig()->setConfigParam("aTrustedIPs", array("xxx"));
        $oUtilsServer = $this->getMock("oxUtilsServer", array("getRemoteAddress"));
        $oUtilsServer->expects($this->once())->method('getRemoteAddress')->will($this->returnValue("xxx"));
        $this->assertTrue($oUtilsServer->isTrustedClientIp());
    }

    public function testGetCookiePathWhenACookiePathsIsSetUp()
    {
        $sShopId = oxRegistry::getConfig()->getShopId();
        modConfig::getInstance()->setConfigParam("aCookiePaths", array($sShopId => 'somepath'));

        $oUtilsServer = new oxUtilsServer();
        $this->assertEquals('somepath', $oUtilsServer->UNITgetCookiePath(""));
    }

    public function testGetCookieDomainWhenACookieDomainsIsSetUp()
    {
        $sShopId = oxRegistry::getConfig()->getShopId();
        modConfig::getInstance()->setConfigParam("aCookieDomains", array($sShopId => 'somedomain'));

        $oUtilsServer = new oxUtilsServer();
        $this->assertEquals('somedomain', $oUtilsServer->UNITgetCookieDomain(""));
    }

    public function testGetCookiePath()
    {
        $oUtilsServer = new oxUtilsServer();
        $this->assertEquals("xxx", $oUtilsServer->UNITgetCookiePath("xxx"));
        $this->assertEquals("", $oUtilsServer->UNITgetCookiePath(null));
    }

    /**
     * test is actually nonsense under unit testing
     * Reason: The testant immediately and explicitly returns on defined('OXID_PHP_UNIT')
     */
    public function testSetCookie()
    {
        $sName = "someName";
        $sValue = "someValue";
        $this->assertNull(oxRegistry::get("oxUtilsServer")->setOxCookie($sName, $sValue));
    }

    public function testGetCookie()
    {
        // $sName = null
        /*  $aCookie = oxRegistry::get("oxUtilsServer")->getOxCookie();
  var_dump($_COOKIE);
  var_dump($aCookie);
          $this->assertTrue((isset($aCookie) && ($aCookie[0] == null)));
          $this->assertNull(oxRegistry::get("oxUtilsServer")->getOxCookie('test'));*/

        $aC = $_COOKIE;
        $e = null;
        try {

            $_COOKIE['test'] = "asd'\"\000aa";
            $this->assertEquals("asd&#039;&quot;aa", oxRegistry::get("oxUtilsServer")->getOxCookie('test'));
        } catch (Exception $e) {
        }

        // restore data
        $_COOKIE = $aC;

        // check if exception has beed thrown
        if ($e) {
            throw $e;
        }
    }

    public function testGetRemoteAddress()
    {
        $sIP = '127.0.0.1';
        // in test mode, there are no remote adresses, thus null
        unset($_SERVER["HTTP_X_FORWARDED_FOR"]);
        unset($_SERVER["HTTP_CLIENT_IP"]);
        if (isset($_SERVER["REMOTE_ADDR"])) {
            $this->assertNull(oxRegistry::get("oxUtilsServer")->getRemoteAddress());
        } else {
            $_SERVER["REMOTE_ADDR"] = $sIP;
            $this->assertEquals(oxRegistry::get("oxUtilsServer")->getRemoteAddress(), $sIP);
        }

        $_SERVER["HTTP_X_FORWARDED_FOR"] = $sIP;
        $this->assertEquals(oxRegistry::get("oxUtilsServer")->getRemoteAddress(), $sIP);
        unset($_SERVER["HTTP_X_FORWARDED_FOR"]);
        $_SERVER["HTTP_CLIENT_IP"] = $sIP;
        $this->assertEquals(oxRegistry::get("oxUtilsServer")->getRemoteAddress(), $sIP);
        unset($_SERVER["HTTP_CLIENT_IP"]);
    }

    public function testGetRemoteAddressProxyUsage()
    {
        $sIP = '127.0.0.1';
        $sProxy = '127.5.4.4';
        // in test mode, there are no remote adresses, thus null
        $_SERVER["HTTP_X_FORWARDED_FOR"] = $sIP . ',' . $sProxy;
        $this->assertEquals(oxRegistry::get("oxUtilsServer")->getRemoteAddress(), $sIP);
        unset($_SERVER["HTTP_X_FORWARDED_FOR"]);
    }

    public function testGetServerVar()
    {
        $sName = md5(uniqid());
        $sValue = time();

        $_SERVER[$sName] = $sValue;;
        $this->assertEquals($sValue, oxRegistry::get("oxUtilsServer")->getServerVar($sName));
        $this->assertEquals($_SERVER, oxRegistry::get("oxUtilsServer")->getServerVar());
    }

    /**
     * Testing user cookie setter, getter and deletion functionality
     */
    public function testGetSetAndDeleteUserCookie()
    {
        oxTestModules::addFunction("oxUtilsDate", "getTime", "{return 0;}");
        $sCryptedVal = 'admin@@@' . crypt('admin', 'test_salt');
        $oUtils = new oxutilsserver();

        $this->assertNull($oUtils->getUserCookie());

        $oUtils->setUserCookie('admin', 'admin', null, 31536000, 'test_salt');
        $this->assertEquals($sCryptedVal, $oUtils->getUserCookie());


        $oUtils->deleteUserCookie();
        $this->assertNull($oUtils->getUserCookie());
    }

    /**
     * oxUtilsServer::processUserAgentInfo test case
     */
    public function testProcessUserAgentInfo()
    {
        $aServerInfo = array("Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)"                                           =>
                                 "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)",

                             "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)"                                           =>
                                 "Mozilla/4.0 (compatible; Windows NT 5.1; Trident/4.0; .NET CLR 1.1.4322; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729; .NET4.0C)",

                             "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8 ( .NET CLR 3.5.30729; .NET4.0C)"                                                                               =>
                                 "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8 ( .NET CLR 3.5.30729; .NET4.0C)",

                             "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)"                                                                        =>
                                 "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)",

                             "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)"                                                                        =>
                                 "Mozilla/4.0 (compatible; Windows NT 5.1; Trident/4.0; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)",

                             "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729)"                                                                                          =>
                                 "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.3) Gecko/20100401 Firefox/3.6.3 (.NET CLR 3.5.30729)",

                             "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)"                               =>
                                 "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)",

                             "Mozilla/4.0 (compatible; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)"                                         =>
                                 "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C)",

                             "Mozilla/5.0 (Windows; U; Windows NT 6.1; lt; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8"                                                                                                                  =>
                                 "Mozilla/5.0 (Windows; U; Windows NT 6.1; lt; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8",

                             "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8"                                                                                                               =>
                                 "Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.8) Gecko/20100722 Firefox/3.6.8",

                             "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; Creative AutoUpdate v1.40.01)" =>
                                 "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; Creative AutoUpdate v1.40.01)",

                             "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; Creative AutoUpdate v1.40.01)" =>
                                 "Mozilla/4.0 (compatible; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; Creative AutoUpdate v1.40.01)",

                             "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; GTB6.5; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)"                                                                =>
                                 "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; GTB6.5; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)",

                             "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; Trident/4.0; GTB6.5; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)"                                                                =>
                                 "Mozilla/4.0 (compatible; Windows NT 5.1; Trident/4.0; GTB6.5; .NET CLR 2.0.50727; .NET CLR 3.0.4506.2152; .NET CLR 3.5.30729)",

                             "Mozilla/5.0 (Windows; U; Windows NT 5.1; lt; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2 ( .NET CLR 3.5.30729)"                                                                                            =>
                                 "Mozilla/5.0 (Windows; U; Windows NT 5.1; lt; rv:1.9.2.2) Gecko/20100316 Firefox/3.6.2 ( .NET CLR 3.5.30729)",

                             "Opera/9.80 (Windows NT 5.1; U; en) Presto/2.6.30 Version/10.60"                                                                                                                                         =>
                                 "Opera/9.80 (Windows NT 5.1; U; en) Presto/2.6.30 Version/10.60",

                             "Mozilla/4.0 (compatible; MSIE 8.0; AOL 9.1; AOLBuild 4334.34; Windows NT 5.1; SV1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) ; .NET CLR 1.1.4322)"                                        =>
                                 "Mozilla/4.0 (compatible; MSIE 7.0; AOL 9.1; AOLBuild 4334.34; Windows NT 5.1; SV1; Mozilla/4.0 (compatible; MSIE 5.0; Windows NT 5.1; SV1) ; .NET CLR 1.1.4322)"

        );

        $oUtils = new oxUtilsServer();
        foreach ($aServerInfo as $sKey => $sVal) {
            $this->assertTrue($oUtils->processUserAgentInfo($sKey) == $oUtils->processUserAgentInfo($sVal));
        }
    }

    public function testGetServerIp()
    {
        $sOldServerIp = $_SERVER['SERVER_ADDR'];

        $sExpectedIP = '192.168.0.9';
        $_SERVER['SERVER_ADDR'] = $sExpectedIP;
        $oUtils = new oxUtilsServer();
        $sServerIp = $oUtils->getServerIp();

        $_SERVER['SERVER_ADDR'] = $sOldServerIp;

        $this->assertSame($sExpectedIP, $sServerIp);
    }

    public function testGetServerNodeIdNotEmpty()
    {
        $oUtilsServer = new oxUtilsServer();
        $sServerId = $oUtilsServer->getServerNodeId();

        $this->assertNotEmpty($sServerId);

        return $sServerId;
    }

    /**
     * @depends testGetServerNodeIdNotEmpty
     */
    public function testGetServerNodeIdReturnSameValue($sServerId1)
    {
        $oUtilsServer = new oxUtilsServer();
        $sServerId2 = $oUtilsServer->getServerNodeId();

        $this->assertSame($sServerId1, $sServerId2);
    }

    /**
     * @depends testGetServerNodeIdNotEmpty
     */
    public function testGetServerNodeIdReturnDifferentValueIfDifferentIp($sServerId1)
    {
        $sOldServerIp = $_SERVER['SERVER_ADDR'];

        $sExpectedIP = '1.168.0.9';
        $_SERVER['SERVER_ADDR'] = $sExpectedIP;

        $oUtilsServer = new oxUtilsServer();
        $sServerId2 = $oUtilsServer->getServerNodeId();

        $_SERVER['SERVER_ADDR'] = $sOldServerIp;

        $this->assertNotSame($sServerId1, $sServerId2);
    }
}
