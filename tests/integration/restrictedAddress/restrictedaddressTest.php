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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath(dirname(__FILE__).'/../../') . '/unit/OxidTestCase.php';


class Integration_RestrictedAddress_RestrictedAddressTest extends OxidTestCase
{
    /**
     * DataProvider return shop URL list to call.
     * @return array
     */
    public function provider_RequestForbiddenMethod_RedirectedWithoutForbiddenRequest()
    {
        $oConfig = $this->getConfig();
        $sShopUrl = $oConfig->getShopMainUrl();
        return array(
            array( $sShopUrl .'?fnc=getShopVersion', $sShopUrl ),
            array( $sShopUrl .'?fnc=getShopEdition', $sShopUrl ),
            array( $sShopUrl .'?fnc=getRevision', $sShopUrl ),
            array( $sShopUrl .'someSeoURL/?fnc=getRevision', $sShopUrl.'someSeoURL/' ),
            array( $sShopUrl .'?fnc=getShopVersion&n2=v2', $sShopUrl ),
            array( $sShopUrl .'?fnc=getShopEdition&n2=v2', $sShopUrl ),
            array( $sShopUrl .'?fnc=getRevision&n2=v2', $sShopUrl ),
            array( $sShopUrl .'someSeoURL/?fnc=getRevision&n2=v2', $sShopUrl.'someSeoURL/' ),
            array( $sShopUrl .'?name=value&fnc=getShopVersion', $sShopUrl ),
            array( $sShopUrl .'?name=value&fnc=getShopEdition', $sShopUrl ),
            array( $sShopUrl .'?name=value&fnc=getRevision', $sShopUrl ),
            array( $sShopUrl .'someSeoURL/?name=value&fnc=getRevision', $sShopUrl.'someSeoURL/' ),
            array( $sShopUrl .'?name=value&fnc=getShopVersion&n2=v2', $sShopUrl ),
            array( $sShopUrl .'?name=value&fnc=getShopEdition&n2=v2', $sShopUrl ),
            array( $sShopUrl .'?name=value&fnc=getRevision&n2=v2', $sShopUrl ),
            array( $sShopUrl .'someSeoURL/?name=value&fnc=getRevision&n2=v2', $sShopUrl.'someSeoURL/' ),
        );
    }

    /**
     * Fix for bug entry 0005569: Oxid leaks internal information to the outside when calling certain urls
     * @dataProvider provider_RequestForbiddenMethod_RedirectedWithoutForbiddenRequest
     */
    public function test_RequestForbiddenMethod_RedirectedWithoutForbiddenRequest($sForbiddenUrl, $sRedirectUrl)
    {
        $sResult = $this->callPage($sForbiddenUrl);

        $sLocation = "Location: ". $sRedirectUrl ."\r\n";
        $this->assertContains( $sLocation, $sResult, 'User should be redirected to same URL without forbidden parameter.' );
    }

    /**
     * Fix for bug 0005565: Accessing config.inc.php directly results in Fatal error
     */
    public function test_configCalled_notAccessed()
    {
        $oConfig = $this->getConfig();
        $sShopUrl = $oConfig->getShopMainUrl();
        $sResult = $this->callPage($sShopUrl.'/config.inc.php');
        $sLocation = "Location: ". $sShopUrl ."index.php\r\n";
        $this->assertContains( $sLocation, $sResult, 'User should be redirected to same URL without forbidden parameter.' );
    }

    public function testAccessHtaccessFile()
    {
        $oConfig = $this->getConfig();
        $sShopUrl = $oConfig->getShopMainUrl();
        $sResult = $this->callPage($sShopUrl.'/.htaccess');
        $this->assertContains( '403 Forbidden', $sResult, 'User should get forbidden page error.' );
    }

    /**
     * @param string $sShopUrl shop url to call.
     * @return string
     */
    private function callPage($sShopUrl)
    {
        $oCurl = new oxCurl();
        $oCurl->setOption('CURLOPT_HEADER', TRUE);

        $oCurl->setUrl($sShopUrl);

        return $oCurl->execute();
    }
}