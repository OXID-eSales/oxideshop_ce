<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \stdClass;
use \oxCompanyVatIn;
use \oxTestModules;

class OnlineVatIdCheckTest extends \OxidTestCase
{
    /**
     * Initialize the fixture.
     */
    protected function setUp(): void
    {
        parent::setUp();

        ini_set("soap.wsdl_cache_enabled", "0");
    }

    /**
     * Testing vat id online checker
     *
     * @group quarantine
     */
    public function testCheckOnlineWithGoodVatId()
    {
        $this->markTestSkipped('TEMPORARY SKIPPING: as vat id check system banned us. Test need to be rewritten to UNIT');

        $iTime = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 480);

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = 'DE';
        $oCheckVat->vatNumber = '231450866';

        $oOnlineVatCheck = oxNew('oxOnlineVatIdCheck');
        if (!$oOnlineVatCheck->isServiceAvailable()) {
            $this->markTestSkipped('VAT check service is not available');
        }

        $blRet = $oOnlineVatCheck->checkOnline($oCheckVat);
        if ('MS_UNAVAILABLE' == $oOnlineVatCheck->getError()) {
            ini_set('default_socket_timeout', $iTime);
            $this->markTestSkipped('member state is unavailable');
        }
        if ('SERVICE_UNAVAILABLE' == $oOnlineVatCheck->getError()) {
            ini_set('default_socket_timeout', $iTime);
            $this->markTestSkipped('The SOAP service is unavailable, try again later');
        }
        $this->assertTrue($blRet, 'Got error: ' . $oOnlineVatCheck->getError());
        ini_set('default_socket_timeout', $iTime);
    }

    /**
     * Testing vat id online checker - with wrong vat id
     *
     * @group quarantine
     */
    public function testCheckOnlineWithWrongVatId()
    {
        $this->markTestSkipped('TEMPORARY SKIPPING: as vat id check system banned us. Test need to be rewritten to UNIT');

        $iTime = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 480);

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = 'ABC';
        $oCheckVat->vatNumber = '111111';

        $oOnlineVatCheck = $this->getProxyClass('oxOnlineVatIdCheck');
        if (!$oOnlineVatCheck->isServiceAvailable()) {
            $this->markTestSkipped('VAT check service is not available');
        }

        $this->assertFalse($oOnlineVatCheck->checkOnline($oCheckVat));
        ini_set('default_socket_timeout', $iTime);
        $this->assertEquals('INVALID_INPUT', $oOnlineVatCheck->getNonPublicVar('_sError'));
    }

    /**
     * Testing vat id online checker - with invalid vat id
     *
     * @group quarantine
     */
    public function testCheckOnlineWithInvalidVatId()
    {
        $this->markTestSkipped('TEMPORARY SKIPPING: as vat id check system banned us. Test need to be rewritten to UNIT');

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = 'DE';
        $oCheckVat->vatNumber = '111111';

        $oOnlineVatCheck = $this->getProxyClass('oxOnlineVatIdCheck');
        if (!$oOnlineVatCheck->isServiceAvailable()) {
            $this->markTestSkipped('VAT check service is not available');
        }
        if ('MS_UNAVAILABLE' == $oOnlineVatCheck->getError()) {
            $this->markTestSkipped('member state is unavailable');
        }
        if ('SERVICE_UNAVAILABLE' == $oOnlineVatCheck->getError()) {
            $this->markTestSkipped('The SOAP service is unavailable, try again later');
        }
        $this->assertFalse($oOnlineVatCheck->checkOnline($oCheckVat));
        if ('SERVER_BUSY' !== $oOnlineVatCheck->getNonPublicVar('_sError')) {
            $this->assertNull($oOnlineVatCheck->getNonPublicVar('_sError'));
        }
    }

    /**
     * Testing vat id online checker - with invalid service
     */
    public function testCheckOnlineWithServiceNotReachable()
    {
        $oOnlineVatIdCheck = $this->getMock($this->getProxyClassName("oxOnlineVatIdCheck"), ["isServiceAvailable"]);
        $oOnlineVatIdCheck->expects($this->once())->method('isServiceAvailable')->will($this->returnValue(false));

        $this->assertEquals(false, $oOnlineVatIdCheck->checkOnline(new stdClass()));
        $this->assertEquals("SERVICE_UNREACHABLE", $oOnlineVatIdCheck->getError());
    }

    /**
     * Testing oxOnlineVatIdCheck::getWsdlUrl()
     */
    public function testGetWsdlUrl_default()
    {
        $oOnline = oxNew('oxOnlineVatIdCheck');
        $this->assertEquals('https://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl', $oOnline->getWsdlUrl());
    }

    /**
     * Testing oxOnlineVatIdCheck::getWsdlUrl()
     */
    public function testGetWsdlUrl_custom()
    {
        $oOnline = oxNew('oxOnlineVatIdCheck');
        $this->getConfig()->setConfigParam("sVatIdCheckInterfaceWsdl", "sVatIdCheckInterfaceWsdl");
        $this->assertEquals("sVatIdCheckInterfaceWsdl", $oOnline->getWsdlUrl());
    }

    public function testValidate()
    {
        $oVatIn = new oxCompanyVatIn('LT1212');

        $oExpect = new stdClass();
        $oExpect->countryCode = 'LT';
        $oExpect->vatNumber = '1212';

        $oOnlineVatCheck = $this->getMock(\OxidEsales\Eshop\Core\OnlineVatIdCheck::class, ['checkOnline']);
        $oOnlineVatCheck->expects($this->once())->method('checkOnline')->with($this->equalTo($oExpect));

        $oOnlineVatCheck->validate($oVatIn);
    }

    public function testValidateOnFailSetError()
    {
        $oVatIn = new oxCompanyVatIn('LT1212');

        $oExpect = new stdClass();
        $oExpect->countryCode = 'LT';
        $oExpect->vatNumber = '1212';

        $oOnlineVatCheck = $this->getMock(\OxidEsales\Eshop\Core\OnlineVatIdCheck::class, ['checkOnline']);
        $oOnlineVatCheck->expects($this->once())->method('checkOnline')->with($this->equalTo($oExpect))->will($this->returnValue(false));

        $this->assertFalse($oOnlineVatCheck->validate($oVatIn));
        $this->assertSame('ID_NOT_VALID', $oOnlineVatCheck->getError());
    }
}
