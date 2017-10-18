<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

/**
 * Class Integration_OnlineInfo_FrontendServersInformationStoringTest
 *
 * @covers \OxidEsales\EshopCommunity\Core\OnlineServerEmailBuilder
 * @covers \OxidEsales\EshopCommunity\Core\SimpleXml
 * @covers \OxidEsales\EshopCommunity\Core\OnlineLicenseCheckCaller
 * @covers \OxidEsales\EshopCommunity\Core\UserCounter
 * @covers \OxidEsales\EshopCommunity\Core\OnlineLicenseCheck
 */
class OnlineLicenseCheckResponseHandlingTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function testRequestHandlingWithPositiveResponse()
    {
        $this->stubExceptionToNotWriteToLog(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class);

        $config = $this->getConfig();
        $config->setConfigParam('blShopStopped', false);
        $config->setConfigParam('sShopVar', '');

        $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $xml .= '<olc>';
        $xml .=   '<code>0</code>';
        $xml .=   '<message>ACK</message>';
        $xml .= '</olc>'."\n";

        $curl = $this->getMockBuilder(\OxidEsales\Eshop\Core\Curl::class)
            ->setMethods(['execute'])
            ->getMock();
        $curl->expects($this->any())->method('execute')->will($this->returnValue($xml));
        /** @var \OxidEsales\Eshop\Core\Curl $curl */

        $emailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);

        $simpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);
        $licenseCaller = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller::class, $curl, $emailBuilder, $simpleXml);

        $userCounter = oxNew(\OxidEsales\Eshop\Core\UserCounter::class);
        $licenseCheck = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, $licenseCaller, $userCounter);

        $licenseCheck->validateShopSerials();

        $this->assertFalse($config->getConfigParam('blShopStopped'));
        $this->assertEquals('', $config->getConfigParam('sShopVar'));
    }

    public function testRequestHandlingWithNegativeResponse()
    {
        $this->stubExceptionToNotWriteToLog(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class);

        if ($this->getTestConfig()->getShopEdition() !== 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        $config = $this->getConfig();
        $config->setConfigParam('blShopStopped', false);
        $config->setConfigParam('sShopVar', '');

        $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $xml .= '<olc>';
        $xml .=   '<code>1</code>';
        $xml .=   '<message>NACK</message>';
        $xml .= '</olc>'."\n";

        $curl = $this->getMockBuilder(\OxidEsales\Eshop\Core\Curl::class)
            ->setMethods(['execute'])
            ->getMock();
        $curl->expects($this->any())->method('execute')->will($this->returnValue($xml));
        /** @var \OxidEsales\Eshop\Core\Curl $curl */

        $emailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);
        $simpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);
        $licenseCaller= oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller::class, $curl, $emailBuilder, $simpleXml);

        $userCounter = oxNew(\OxidEsales\Eshop\Core\UserCounter::class);
        $licenseCheck = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, $licenseCaller, $userCounter);

        $licenseCheck->validateShopSerials();

        $this->assertFalse($config->getConfigParam('blShopStopped'));
        $this->assertNotEquals('unlc', $config->getConfigParam('sShopVar'));
    }

    public function testRequestHandlingWithInvalidResponse()
    {
        $this->stubExceptionToNotWriteToLog(\OxidEsales\Eshop\Core\Exception\SystemComponentException::class);

        $config = $this->getConfig();
        $config->setConfigParam('blShopStopped', false);
        $config->setConfigParam('sShopVar', '');

        $xml = '<?xml version="1.0" encoding="utf-8"?>'."\n";
        $xml .= 'Some random XML'."\n";

        $curl = $this->getMockBuilder(\OxidEsales\Eshop\Core\Curl::class)
            ->setMethods(['execute'])
            ->getMock();
        $curl->expects($this->any())->method('execute')->will($this->returnValue($xml));
        /** @var \OxidEsales\Eshop\Core\Curl $curl */

        $emailBuilder = oxNew(\OxidEsales\Eshop\Core\OnlineServerEmailBuilder::class);
        $simpleXml = oxNew(\OxidEsales\Eshop\Core\SimpleXml::class);
        $licenseCaller = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheckCaller::class, $curl, $emailBuilder, $simpleXml);

        $userCounter = oxNew(\OxidEsales\Eshop\Core\UserCounter::class);
        $licenseCheck = oxNew(\OxidEsales\Eshop\Core\OnlineLicenseCheck::class, $licenseCaller, $userCounter);

        $licenseCheck->validateShopSerials();

        $this->assertFalse($config->getConfigParam('blShopStopped'));
        $this->assertEquals('', $config->getConfigParam('sShopVar'));
    }
}
