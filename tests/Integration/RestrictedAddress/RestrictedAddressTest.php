<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\RestrictedAddress;

class RestrictedAddressTest extends \OxidTestCase
{
    /**
     * Fix for bug 0005565: Accessing config.inc.php directly results in Fatal error
     */
    public function test_configCalled_notAccessed()
    {
        $sShopUrl = $this->getConfig()->getShopMainUrl();
        $sResult = $this->getPageResult('/config.inc.php');
        $sLocation = "Location: " . $sShopUrl . "index.php\r\n";
        $this->assertStringContainsString($sLocation, $sResult, 'User should be redirected to same URL without forbidden parameter.');
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
        $sResultPage = $this->getPageResult($sFilePath);

        $this->assertStringContainsString('Forbidden', $sResultPage, 'User should see forbidden page message.');
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
        $sResultPage = $this->getPageResult($sFilePath);

        $this->assertStringNotContainsString('Forbidden', $sResultPage, "User shouldn't see forbidden page message.");
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
    private function getPageResult($sFilePath)
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
