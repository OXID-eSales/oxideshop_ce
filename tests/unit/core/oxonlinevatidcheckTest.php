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

class Unit_Core_oxOnlineVatIdCheckTest extends OxidTestCase
{

    /**
     * Initialize the fixture.
     */
    protected function setUp()
    {
        parent::setUp();

        ini_set("soap.wsdl_cache_enabled", "0");
    }

    /**
     * Tests oxOnlineVatIdCheck::catchWarning()
     */
    public function testCatchWarning()
    {
        oxTestModules::addFunction('oxUtils', 'writeToLog', '{ return $aA; }');

        $oOnlineVatIdCheck = new oxOnlineVatIdCheck();
        $aResult = $oOnlineVatIdCheck->catchWarning(1, 1, 1, 1);

        $this->assertEquals(array("Warning: 1 in 1 on line 1", "EXCEPTION_LOG.txt"), $aResult);
    }

    /**
     * Test for #0001483: Wrong message when entering an invalid VAT-ID during registration
     */
    public function testForBugEntry1483()
    {
        try {
            $oOnlineVatIdCheck = $this->getMock("oxOnlineVatIdCheck", array("_checkOnline", "_getError"));
            $oOnlineVatIdCheck->expects($this->once())->method('_checkOnline')->will($this->returnValue(false));
            $oOnlineVatIdCheck->expects($this->once())->method('_getError')->will($this->returnValue(false));
            $oOnlineVatIdCheck->checkUid("testVatId");
        } catch (Exception $oExcp) {
            $this->assertEquals("VAT_MESSAGE_ID_NOT_VALID", $oExcp->getMessage());

            return;
        }

        $this->fail("error running testForBugEntry1483");
    }

    public function testCheckUidTakesFromCacheTrue()
    {
        oxTestModules::addFunction('oxOnlineVatIdCheck', 'setCache', '{ oxOnlineVatIdCheck::$_aVatCheckCache = $aA[0]; }');
        oxNew('oxOnlineVatIdCheck')->setCache(array('DE123456789' => true));

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = 'DE';
        $oCheckVat->vatNumber = '123456789';

        $oOnlineVatCheck = $this->getMock('oxOnlineVatIdCheck', array('_checkOnline'));
        $oOnlineVatCheck->expects($this->never())->method('_checkOnline');

        try {
            $this->assertTrue($oOnlineVatCheck->checkUid('DE123456789'));
        } catch (Exception $oException) {
            $this->fail();
        }
    }

    public function testCheckUidTakesFromCacheError()
    {
        oxTestModules::addFunction('oxOnlineVatIdCheck', 'setCache', '{ oxOnlineVatIdCheck::$_aVatCheckCache = $aA[0]; }');
        oxNew('oxOnlineVatIdCheck')->setCache(array('DE123456789' => 'SERVER_BUSY'));

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = 'DE';
        $oCheckVat->vatNumber = '123456789';

        $oOnlineVatCheck = $this->getMock('oxOnlineVatIdCheck', array('_checkOnline'));
        $oOnlineVatCheck->expects($this->never())->method('_checkOnline');

        try {
            $oOnlineVatCheck->checkUid('DE123456789');
        } catch (oxConnectionException $oException) {
            $this->assertEquals('VAT_MESSAGE_SERVER_BUSY', $oException->getMessage());

            return;
        }
        $this->fail();
    }

    /**
     * Testing vat id checker - good vat id
     */
    public function testCheckUidWithGoodVatId()
    {

        oxTestModules::addFunction('oxOnlineVatIdCheck', 'clearCache', '{ oxOnlineVatIdCheck::$_aVatCheckCache = array(); }');
        oxNew('oxOnlineVatIdCheck')->clearCache();

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = 'DE';
        $oCheckVat->vatNumber = '123456789';

        $oOnlineVatCheck = $this->getMock('oxOnlineVatIdCheck', array('_checkOnline'));
        $oOnlineVatCheck->expects($this->once())->method('_checkOnline')->with($this->equalTo($oCheckVat))->will($this->returnValue(true));

        try {
            $this->assertTrue($oOnlineVatCheck->checkUid('DE123456789'));
        } catch (Exception $oException) {
            $this->fail();
        }
    }

    /**
     * Testing vat id checker - not existing (but correct format) vat id
     */
    public function testCheckUidWithNotExistingVatId()
    {

        oxTestModules::addFunction('oxOnlineVatIdCheck', 'clearCache', '{ oxOnlineVatIdCheck::$_aVatCheckCache = array(); }');
        oxNew('oxOnlineVatIdCheck')->clearCache();

        $oOnlineVatCheck = $this->getMock('oxOnlineVatIdCheck', array('_checkOnline', '_getError'));
        $oOnlineVatCheck->expects($this->once())->method('_checkOnline')->will($this->returnValue(false));
        $oOnlineVatCheck->expects($this->once())->method('_getError')->will($this->returnValue('INVALID_INPUT'));

        try {
            $this->assertTrue($oOnlineVatCheck->checkUid('DE123456789'));
        } catch (oxInputException $oException) {
            //OK
            $this->assertEquals('VAT_MESSAGE_INVALID_INPUT', $oException->getMessage());

            return;
        }

        $this->fail('Failed checking not existing, but correct format vat id');
    }

    /**
     * Testing vat id checker - when soap returns error msg
     */
    public function testCheckUidWhenSoapReturnsErrorMsg()
    {

        oxTestModules::addFunction('oxOnlineVatIdCheck', 'clearCache', '{ oxOnlineVatIdCheck::$_aVatCheckCache = array(); }');
        oxNew('oxOnlineVatIdCheck')->clearCache();

        $oOnlineVatCheck = $this->getMock('oxOnlineVatIdCheck', array('_checkOnline', '_getError'));
        $oOnlineVatCheck->expects($this->once())->method('_checkOnline')->will($this->returnValue(false));
        $oOnlineVatCheck->expects($this->once())->method('_getError')->will($this->returnValue('MY_ERROR_MSG'));

        try {
            $this->assertTrue($oOnlineVatCheck->checkUid('DE123456789'));
        } catch (oxConnectionException $oException) {
            //OK
            $this->assertEquals('VAT_MESSAGE_MY_ERROR_MSG', $oException->getMessage());

            return;
        }

        $this->fail('Failed checking vat id when soap returns error msg');
    }

    /**
     * Testing vat id online checker
     */
    public function testCheckOnlineWithGoodVatId()
    {
        $this->markTestSkipped('TEMPORARY SKIPPING: as vat id check system banned us. Test need to be rewritten to UNIT');

        $iTime = ini_get('default_socket_timeout');
        ini_set('default_socket_timeout', 480);

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = 'DE';
        $oCheckVat->vatNumber = '231450866';

        $oOnlineVatCheck = new oxOnlineVatIdCheck();
        if (!$oOnlineVatCheck->UNITisServiceAvailable()) {
            $this->markTestSkipped('VAT check service is not available');
        }

        $blRet = $oOnlineVatCheck->UNITcheckOnline($oCheckVat);
        if ('MS_UNAVAILABLE' == $oOnlineVatCheck->UNITgetError()) {
            ini_set('default_socket_timeout', $iTime);
            $this->markTestSkipped('member state is unavailable');
        }
        if ('SERVICE_UNAVAILABLE' == $oOnlineVatCheck->UNITgetError()) {
            ini_set('default_socket_timeout', $iTime);
            $this->markTestSkipped('The SOAP service is unavailable, try again later');
        }
        $this->assertTrue($blRet, 'Got error: ' . $oOnlineVatCheck->UNITgetError());
        ini_set('default_socket_timeout', $iTime);
    }

    /**
     * Testing vat id online checker - with wrong vat id
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
        if (!$oOnlineVatCheck->UNITisServiceAvailable()) {
            $this->markTestSkipped('VAT check service is not available');
        }

        $this->assertFalse($oOnlineVatCheck->UNITcheckOnline($oCheckVat));
        ini_set('default_socket_timeout', $iTime);
        $this->assertEquals('INVALID_INPUT', $oOnlineVatCheck->getNonPublicVar('_sError'));

    }

    /**
     * Testing vat id online checker - with invalid vat id
     */
    public function testCheckOnlineWithInvalidVatId()
    {
        $this->markTestSkipped('TEMPORARY SKIPPING: as vat id check system banned us. Test need to be rewritten to UNIT');

        $oCheckVat = new stdClass();
        $oCheckVat->countryCode = 'DE';
        $oCheckVat->vatNumber = '111111';

        $oOnlineVatCheck = $this->getProxyClass('oxOnlineVatIdCheck');
        if (!$oOnlineVatCheck->UNITisServiceAvailable()) {
            $this->markTestSkipped('VAT check service is not available');
        }
        if ('MS_UNAVAILABLE' == $oOnlineVatCheck->UNITgetError()) {
            $this->markTestSkipped('member state is unavailable');
        }
        if ('SERVICE_UNAVAILABLE' == $oOnlineVatCheck->UNITgetError()) {
            $this->markTestSkipped('The SOAP service is unavailable, try again later');
        }
        $this->assertFalse($oOnlineVatCheck->UNITcheckOnline($oCheckVat));
        if ('SERVER_BUSY' !== $oOnlineVatCheck->getNonPublicVar('_sError')) {
            $this->assertNull($oOnlineVatCheck->getNonPublicVar('_sError'));
        }
    }

    /**
     * Testing vat id online checker - with invalid service
     */
    public function testCheckOnlineWithServiceNotReachable()
    {
        $oOnlineVatIdCheck = $this->getMock($this->getProxyClassName("oxOnlineVatIdCheck"), array("_isServiceAvailable"));
        $oOnlineVatIdCheck->expects($this->once())->method('_isServiceAvailable')->will($this->returnValue(false));

        $this->assertEquals(false, $oOnlineVatIdCheck->UNITcheckOnline(new stdClass()));
        $this->assertEquals("SERVICE_UNREACHABLE", $oOnlineVatIdCheck->getError());
    }

    /**
     * Testing getting error msg - no error
     */
    public function testGetErrorWithNoError()
    {
        $oOnlineVatCheck = $this->getProxyClass('oxOnlineVatIdCheck');
        $oOnlineVatCheck->setNonPublicVar('_sError', null);

        $this->assertSame('', $oOnlineVatCheck->UNITgetError());
    }

    /**
     * Testing getting error msg - with setted error msg
     */
    public function testGetErrorWithError()
    {
        $oOnlineVatCheck = $this->getProxyClass('oxOnlineVatIdCheck');
        $sErrMsg = "soapenv:Server.userException: javax.xml.rpc.soap.SOAPFaultException: { 'INVALID_INPUT' }";
        $oOnlineVatCheck->setNonPublicVar('_sError', $sErrMsg);

        $this->assertEquals('INVALID_INPUT', $oOnlineVatCheck->UNITgetError());
    }

    /**
     * Testing getting error msg - when unknown error msg occurs
     */
    public function testGetErrorWithUnknownErrorMsg()
    {
        $oOnlineVatCheck = $this->getProxyClass('oxOnlineVatIdCheck');
        $sErrMsg = "soapenv:Server.userException: bla bla bla bla ";
        $oOnlineVatCheck->setNonPublicVar('_sError', $sErrMsg);

        $this->assertEquals('SERVICE_UNAVAILABLE', $oOnlineVatCheck->UNITgetError());
    }

    /**
     * Testing oxOnlineVatIdCheck::getWsdlUrl()
     */
    public function testGetWsdlUrl_default()
    {
        $oOnline = new oxOnlineVatIdCheck();
        $this->assertEquals('http://ec.europa.eu/taxation_customs/vies/checkVatService.wsdl', $oOnline->getWsdlUrl());
    }

    /**
     * Testing oxOnlineVatIdCheck::getWsdlUrl()
     */
    public function testGetWsdlUrl_custom()
    {
        $oOnline = new oxOnlineVatIdCheck();
        $this->getConfig()->setConfigParam("sVatIdCheckInterfaceWsdl", "sVatIdCheckInterfaceWsdl");
        $this->assertEquals("sVatIdCheckInterfaceWsdl", $oOnline->getWsdlUrl());
    }

    /**
     * Testing oxOnlineVatIdCheck::isDisabled()
     */
    public function testIsDisabled()
    {
        $oOnline = new oxOnlineVatIdCheck();
        $this->assertEquals(false, $oOnline->isDisabled());

        $this->getConfig()->setConfigParam("blVatIdCheckDisabled", "blVatIdCheckDisabled");
        $this->assertEquals(true, $oOnline->isDisabled());

    }

    public function testValidate()
    {
        $oVatIn = new oxCompanyVatIn('LT1212');

        $oExpect = new stdClass();
        $oExpect->countryCode = 'LT';
        $oExpect->vatNumber = '1212';

        $oOnlineVatCheck = $this->getMock('oxOnlineVatIdCheck', array('_checkOnline'));
        $oOnlineVatCheck->expects($this->once())->method('_checkOnline')->with($this->equalTo($oExpect));

        $oOnlineVatCheck->validate($oVatIn);
    }

    public function testValidateOnFailSetError()
    {
        $oVatIn = new oxCompanyVatIn('LT1212');

        $oExpect = new stdClass();
        $oExpect->countryCode = 'LT';
        $oExpect->vatNumber = '1212';

        $oOnlineVatCheck = $this->getMock('oxOnlineVatIdCheck', array('_checkOnline'));
        $oOnlineVatCheck->expects($this->once())->method('_checkOnline')->with($this->equalTo($oExpect))->will($this->returnValue(false));

        $this->assertFalse($oOnlineVatCheck->validate($oVatIn));
        $this->assertSame('ID_NOT_VALID', $oOnlineVatCheck->getError());
    }
}