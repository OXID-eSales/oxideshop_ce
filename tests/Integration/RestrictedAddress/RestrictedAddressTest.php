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
namespace OxidEsales\EshopCommunity\Tests\Integration\RestrictedAddress;

/**
 * Class Integration_RestrictedAddress_RestrictedAddressTest
 */
class RestrictedAddressTest extends \OxidTestCase
{
    /**
     * DataProvider returns shop URL list to call.
     *
     * @return array
     */
    public function providerRequestFunctionThatResultsInNoValidNewActionGetsRedirectedToStart()
    {
        $shopUrl = $this->getConfig()->getShopMainUrl();

        return array(
            array($shopUrl . '?fnc=getShopVersion'),
            array($shopUrl . '?fnc=getShopEdition'),
            array($shopUrl . '?fnc=getShopVersion&n2=v2'),
            array($shopUrl . '?fnc=getShopEdition&n2=v2'),
            array($shopUrl . '?name=value&fnc=getShopVersion'),
            array($shopUrl . '?name=value&fnc=getShopEdition'),
            array($shopUrl . '?name=value&fnc=getShopVersion&n2=v2'),
            array($shopUrl . '?name=value&fnc=getShopEdition&n2=v2'),
            array($shopUrl . '?fnc=%67etshopversion'),
            array($shopUrl . '?fnc=getCharSet'),
            array($shopUrl . '?fnc=getShopFullEdition'),
            array($shopUrl . '?fnc=isMall'),
            array($shopUrl . '?fnc=getCacheLifeTime'),
            array($shopUrl . '?fnc=addGlobalParams')
        );
    }

    /**
     * Test case that a function's return value is no callable new action, directly redirect
     * to startpage without trying to call a not extisting view class.
     *
     * @dataProvider providerRequestFunctionThatResultsInNoValidNewActionGetsRedirectedToStart
     */
    public function _testRequestFunctionThatResultsInNoValidNewActionGetsRedirectedToStart($sForbiddenUrl)
    {
        $shopUrl = $this->getConfig()->getShopMainUrl();

        $result = $this->callPage($sForbiddenUrl);

        $location = "Location: " .  $shopUrl . 'index.php?force_sid=' . $this->extractSessionId($result) .
                     "&cl=start&redirected=1\r\n";
        $this->assertContains($location, $result, 'User should be redirected to shop front page.');
    }

    /**
     * DataProvider returns shop URL list to call.
     *
     * @return array
     */
    public function providerRequestGetRevisionThatResultsInNoValidNewActionGetsRedirectedToStart()
    {
        $shopUrl = $this->getConfig()->getShopMainUrl();

        return array(
                array($shopUrl . '?fnc=getRevision'),
                array($shopUrl . 'Startseite/?fnc=getRevision'),
                array($shopUrl . '?fnc=getRevision&n2=v2'),
                array($shopUrl . 'Startseite/?fnc=getRevision&n2=v2'),
                array($shopUrl . '?name=value&fnc=getRevision'),
                array($shopUrl . 'Startseite/?name=value&fnc=getRevision'),
                array($shopUrl . '?name=value&fnc=getRevision&n2=v2'),
                array($shopUrl . 'Startseite/?name=value&fnc=getRevision&n2=v2')
        );
    }

    /**
     * Same test as before for function call to getRevision. In case we have no revision
     * no new action is called, if function getRevision returns a value, shop redirects
     * to start page as the return value is no valid view class.
     *
     * @dataProvider providerRequestGetRevisionThatResultsInNoValidNewActionGetsRedirectedToStart
     */
    public function testRequestGetRevisionThatResultsInNoValidNewActionGetsRedirectedToStart($forbiddenUrl)
    {
        $shopUrl = $this->getConfig()->getShopMainUrl();

        $result = $this->callPage($forbiddenUrl);

        $location = "Location: " .  $shopUrl . 'index.php?force_sid=' . $this->extractSessionId($result) .
                     "&cl=start&redirected=1\r\n";
        if (false == $this->getConfig()->getRevision()) {
            $this->assertNotContains("Location:", $result, 'No revision means no redirect, no Location header');
        } else {
            $this->assertContains($location, $result, 'User should be redirected to shop front page.');
        }

    }

    /**
     * Fix for bug 0005565: Accessing config.inc.php directly results in Fatal error
     */
    public function test_configCalled_notAccessed()
    {
        $sShopUrl = $this->getConfig()->getShopMainUrl();
        $sResult = $this->_getPageResult('/config.inc.php');
        $sLocation = "Location: " . $sShopUrl . "index.php\r\n";
        $this->assertContains($sLocation, $sResult, 'User should be redirected to same URL without forbidden parameter.');
    }

    public function providerForbiddenFilesAccessibility()
    {
        return array(
            array('/log/EXCEPTION_LOG.txt'),
            array('/log/anything'),
            array('/Application/views/azure/tpl/widget/rss.tpl'),
            array('/pkg.info'),
            array('/op.ini'),
            array('/.htaccess'),
            array('/.ht'),
        );
    }

    /**
     * @param string $sFilePath Path to forbidden file.
     *
     * @dataProvider providerForbiddenFilesAccessibility
     */
    public function testCheckForbiddenFilesAccessibility($sFilePath)
    {
        $sResultPage = $this->_getPageResult($sFilePath);

        $this->assertContains('Forbidden', $sResultPage, 'User should see forbidden page message.');
    }

    public function providerCheckAllowedFilesAccessibility()
    {
        return array(
            array('/op.ini.php'),
            array('/Application/views/azure/tpl/widget/rss.tpl.whatever'),
        );
    }

    /**
     * @param string $sFilePath Path to allowable file.
     *
     * @dataProvider providerCheckAllowedFilesAccessibility
     */
    public function testCheckAllowedFilesAccessibility($sFilePath)
    {
        $sResultPage = $this->_getPageResult($sFilePath);

        $this->assertNotContains('Forbidden', $sResultPage, "User shouldn't see forbidden page message.");
    }

    /**
     * @param string $sShopUrl shop url to call.
     *
     * @return string
     */
    private function callPage($sShopUrl)
    {
        $oCurl = oxNew('oxCurl');
        $oCurl->setOption('CURLOPT_HEADER', true);
        $oCurl->setUrl($sShopUrl);

        return $oCurl->execute();
    }

    /**
     * @param $sFilePath
     *
     * @return string
     */
    private function _getPageResult($sFilePath)
    {
        $sShopUrl = $this->getConfig()->getShopMainUrl();
        $sResultPage = $this->callPage($sShopUrl . $sFilePath);

        return $sResultPage;
    }

    /**
     * Test helper to extract session id form curl response
     *
     * @param $text
     *
     * @return string
     */
    private function extractSessionId($text)
    {
        $parts = explode('Set-Cookie: sid=', $text);
        $parts = explode(';', $parts[1]);
        return trim($parts[0]);
    }
}
