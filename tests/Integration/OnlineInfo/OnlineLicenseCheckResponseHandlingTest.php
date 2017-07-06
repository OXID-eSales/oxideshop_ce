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
namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

use \oxCurl;
use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use OxidEsales\EshopCommunity\Core\Exception\SystemComponentException;
use \oxRegistry;
use \oxSystemComponentException;
use \oxTestModules;

/**
 * Class Integration_OnlineInfo_FrontendServersInformationStoringTest
 *
 * @covers OnlineServerEmailBuilder
 * @covers oxOnlineCaller
 * @covers oxSimpleXml
 * @covers oxOnlineLicenseCheckCaller
 * @covers oxUserCounter
 * @covers oxOnlineLicenseCheck
 */
class OnlineLicenseCheckResponseHandlingTest extends \oxUnitTestCase
{
    public function testRequestHandlingWithPositiveResponse()
    {
        $this->stubExceptionToNotWriteToLog(SystemComponentException::class);

        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('blShopStopped', false);
        $oConfig->setConfigParam('sShopVar', '');

        $sXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $sXml .= '<olc>';
        $sXml .=   '<code>0</code>';
        $sXml .=   '<message>ACK</message>';
        $sXml .= '</olc>'."\n";

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($sXml));
        /** @var oxCurl $oCurl */

        $oEmailBuilder = oxNew(OnlineServerEmailBuilder::class);

        $oSimpleXml = oxNew('oxSimpleXml');
        $oLicenseCaller = oxNew('oxOnlineLicenseCheckCaller', $oCurl, $oEmailBuilder, $oSimpleXml);

        $oUserCounter = oxNew('oxUserCounter');
        $oLicenseCheck = oxNew('oxOnlineLicenseCheck', $oLicenseCaller, $oUserCounter);

        $oLicenseCheck->validateShopSerials();

        $this->assertFalse($oConfig->getConfigParam('blShopStopped'));
        $this->assertEquals('', $oConfig->getConfigParam('sShopVar'));
    }

    public function testRequestHandlingWithNegativeResponse()
    {
        $this->stubExceptionToNotWriteToLog(SystemComponentException::class);

        if ($this->getTestConfig()->getShopEdition() !== 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('blShopStopped', false);
        $oConfig->setConfigParam('sShopVar', '');

        $sXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $sXml .= '<olc>';
        $sXml .=   '<code>1</code>';
        $sXml .=   '<message>NACK</message>';
        $sXml .= '</olc>'."\n";

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($sXml));
        /** @var oxCurl $oCurl */

        $oEmailBuilder = oxNew(OnlineServerEmailBuilder::class);
        $oSimpleXml = oxNew('oxSimpleXml');
        $oLicenseCaller = oxNew('oxOnlineLicenseCheckCaller', $oCurl, $oEmailBuilder, $oSimpleXml);

        $oUserCounter = oxNew('oxUserCounter');
        $oLicenseCheck = oxNew('oxOnlineLicenseCheck', $oLicenseCaller, $oUserCounter);

        $oLicenseCheck->validateShopSerials();

        $this->assertFalse($oConfig->getConfigParam('blShopStopped'));
        $this->assertNotEquals('unlc', $oConfig->getConfigParam('sShopVar'));
    }

    public function testRequestHandlingWithInvalidResponse()
    {
        $this->stubExceptionToNotWriteToLog(SystemComponentException::class);

        $oConfig = oxRegistry::getConfig();
        $oConfig->setConfigParam('blShopStopped', false);
        $oConfig->setConfigParam('sShopVar', '');

        $sXml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $sXml .= 'Some random XML'."\n";

        $oCurl = $this->getMock(\OxidEsales\Eshop\Core\Curl::class, array('execute'));
        $oCurl->expects($this->any())->method('execute')->will($this->returnValue($sXml));
        /** @var oxCurl $oCurl */

        $oEmailBuilder = oxNew(OnlineServerEmailBuilder::class);
        $oSimpleXml = oxNew('oxSimpleXml');
        $oLicenseCaller = oxNew('oxOnlineLicenseCheckCaller', $oCurl, $oEmailBuilder, $oSimpleXml);

        $oUserCounter = oxNew('oxUserCounter');
        $oLicenseCheck = oxNew('oxOnlineLicenseCheck', $oLicenseCaller, $oUserCounter);

        $oLicenseCheck->validateShopSerials();

        $this->assertFalse($oConfig->getConfigParam('blShopStopped'));
        $this->assertEquals('', $oConfig->getConfigParam('sShopVar'));
    }
}
