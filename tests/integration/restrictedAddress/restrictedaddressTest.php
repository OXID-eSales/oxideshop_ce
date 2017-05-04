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
 * Class Integration_RestrictedAddress_RestrictedAddressTest
 */
class Integration_RestrictedAddress_RestrictedAddressTest extends OxidTestCase
{

    /**
     * DataProvider returns shop URL list to call.
     *
     * @return array
     */
    public function providerRequestFunctionThatResultsInNoValidNewActionGetsRedirectedToStart()
    {
        $sShopUrl = $this->getConfig()->getShopMainUrl();

        return array(
            array($sShopUrl . '?fnc=getShopVersion'),
            array($sShopUrl . '?fnc=getShopEdition'),
            array($sShopUrl . '?fnc=getShopVersion&n2=v2'),
            array($sShopUrl . '?fnc=getShopEdition&n2=v2'),
            array($sShopUrl . '?name=value&fnc=getShopVersion'),
            array($sShopUrl . '?name=value&fnc=getShopEdition'),
            array($sShopUrl . '?name=value&fnc=getShopVersion&n2=v2'),
            array($sShopUrl . '?name=value&fnc=getShopEdition&n2=v2'),
            array($sShopUrl . '?fnc=%67etshopversion'),
            array($sShopUrl . '?fnc=getCharSet'),
            array($sShopUrl . '?fnc=getShopFullEdition'),
            array($sShopUrl . '?fnc=addGlobalParams')
        );
    }

    /**
     * Test case that a function's return value is no callable new action, directly redirect
     * to startpage without trying to call a not extisting view class.
     *
     * @dataProvider providerRequestFunctionThatResultsInNoValidNewActionGetsRedirectedToStart
     */
    public function testRequestFunctionThatResultsInNoValidNewActionGetsRedirectedToStart($sForbiddenUrl)
    {
        if ( 0 != $this->getConfig()->getConfigParam('iDebug')) {
            $this->markTestSkipped('Shop does not redirect in debugmode.');
        }

        $sShopUrl = $this->getConfig()->getShopMainUrl();

        $sResult = $this->callPage($sForbiddenUrl);

        $sLocation = "Location: " .  $sShopUrl . 'index.php?force_sid=' . $this->_extractSessionId($sResult) .
                     "&cl=start&redirected=1\r\n";
        $this->assertContains($sLocation, $sResult, 'User should be redirected to shop front page.');
    }

    /**
     * Test case that a function's return value is no callable new action.
     * When shop is in debugmode -1, exception is displayed.
     *
     * @dataProvider providerRequestFunctionThatResultsInNoValidNewActionGetsRedirectedToStart
     */
    public function testRequestFunctionThatResultsInNoValidNewActionDebugModeException($sForbiddenUrl)
    {
        if ( 0 == $this->getConfig()->getConfigParam('iDebug')) {
            $this->markTestSkipped('Test is only for debugmode.');
        }

        $sResult = $this->callPage($sForbiddenUrl);

        $sMessage = 'oxView->_executeNewAction';
        $this->assertContains($sMessage, $sResult, 'User should see an error message.');
    }

    /**
     * DataProvider returns shop URL list to call.
     *
     * @return array
     */
    public function providerRequestGetRevisionThatResultsInNoValidNewActionGetsRedirectedToStart()
    {
        $sShopUrl = $this->getConfig()->getShopMainUrl();

        return array(
                array($sShopUrl . '?fnc=getRevision'),
                array($sShopUrl . 'Startseite/?fnc=getRevision'),
                array($sShopUrl . '?fnc=getRevision&n2=v2'),
                array($sShopUrl . 'Startseite/?fnc=getRevision&n2=v2'),
                array($sShopUrl . '?name=value&fnc=getRevision'),
                array($sShopUrl . 'Startseite/?name=value&fnc=getRevision'),
                array($sShopUrl . '?name=value&fnc=getRevision&n2=v2'),
                array($sShopUrl . 'Startseite/?name=value&fnc=getRevision&n2=v2')
        );
    }

    /**
     * Same test as before for function call to getRevision. In case we have no revision
     * no new action is called, if function getRevision returns a value, shop redirects
     * to start page as the return value is no valid view class.
     *
     * @dataProvider providerRequestGetRevisionThatResultsInNoValidNewActionGetsRedirectedToStart
     */
    public function testRequestGetRevisionThatResultsInNoValidNewActionGetsRedirectedToStart($sForbiddenUrl)
    {
        if ( 0 != $this->getConfig()->getConfigParam('iDebug')) {
            $this->markTestSkipped('Shop does not redirect in debugmode.');
        }

        $sShopUrl = $this->getConfig()->getShopMainUrl();

        $sResult = $this->callPage($sForbiddenUrl);

        $sLocation = "Location: " .  $sShopUrl . 'index.php?force_sid=' . $this->_extractSessionId($sResult) .
                     "&cl=start&redirected=1\r\n";
        if (false == $this->getConfig()->getRevision()) {
            $this->assertNotContains("Location:", $sResult, 'No revision means no redirect, no Location header');
        } else {
            $this->assertContains($sLocation, $sResult, 'User should be redirected to shop front page.');
        }
    }

    /**
     * Same test as before for function call to getRevision. In case we have no revision
     * no new action is called, if function getRevision returns a value, shop redirects
     * to start page as the return value is no valid view class.
     *
     * @dataProvider providerRequestGetRevisionThatResultsInNoValidNewActionGetsRedirectedToStart
     */
    public function testRequestGetRevisionThatResultsInNoValidNewActionDebugmodeException($sForbiddenUrl)
    {
        if ( 0 == $this->getConfig()->getConfigParam('iDebug')) {
            $this->markTestSkipped('Test is only for debugmode.');
        }

        $sResult = $this->callPage($sForbiddenUrl);

        if (false == $this->getConfig()->getRevision()) {
            $this->assertNotContains("Location:", $sResult, 'No revision means no redirect, no Location header');
        } else {
            $sMessage = 'oxView->_executeNewAction';
            $this->assertContains($sMessage, $sResult, 'User should see an error message.');
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
            array('/application/views/azure/tpl/widget/rss.tpl'),
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
            array('/application/views/azure/tpl/widget/rss.tpl.whatever'),
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
        $oCurl = new oxCurl();
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
     * @param $sText
     *
     * @return string
     */
    private function _extractSessionId($sText)
    {
        $aParts = explode('Set-Cookie: sid=', $sText);
        $aParts = explode(';', $aParts[1]);
        return trim($aParts[0]);
    }
}
