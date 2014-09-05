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

class Unit_Core_oxOnlineLicenseCheckTest extends OxidTestCase
{
    public function testRequestFormation()
    {
        $iAdminUsers = 25;
        $iSubShops = 5;
        $aServers = array('7da43ed884a1ad1d6035d4c1d630fc4e' => array(
            'id' => '7da43ed884a1ad1d6035d4c1d630fc4e',
            'timestamp' => '1409911182',
            'ip' => null,
            'lastFrontendUsage' => '1409911182',
            'lastAdminUsage' => null,
        ));

        $oConfig = $this->getMock('oxConfig', array('getMandateCount'));
        $oConfig->expects($this->any())->method('getMandateCount')->will($this->returnValue($iSubShops));
        /** @var oxConfig $oConfig */
        $oConfig->setConfigParam('aServersData', $aServers);
        $this->setConfigParam('aServersData', $aServers);
        oxRegistry::set('oxConfig', $oConfig);

        $oRequest = new oxOnlineLicenseCheckRequest();
        $oRequest->revision = oxRegistry::getConfig()->getRevision();
        $oRequest->pVersion = '1.0';
        $oRequest->productId = 'eShop';
        $oRequest->keys = new stdClass();
        $oRequest->keys->key = array('validSerial');

        $oServers = new stdClass();
        $oServers->server = $aServers;
        $oRequest->productSpecificInformation = new stdClass();
        $oRequest->productSpecificInformation->servers = $oServers;

        $oCounter = new stdClass();
        $oCounter->name = 'admin users';
        $oCounter->value = $iAdminUsers;

        $oSubShops = new stdClass();
        $oSubShops->name = 'subShops';
        $oSubShops->value = $iSubShops;

        $oCounters = new stdClass();
        $oCounters->counter = array($oCounter, $oSubShops);
        $oRequest->productSpecificInformation->counters = $oCounters;

        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->once())->method('doRequest')->with($oRequest);
        /** @var oxOnlineLicenseCheckCaller $oCaller */

        $oUserCounter = $this->getMock('oxUserCounter', array('getAdminCount'), array(), '', false);
        $oUserCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue(25));
        /** @var oxUserCounter $oUserCounter */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller, $oUserCounter);
        $oLicenseCheck->validate('validSerial');
    }

    /**
     * Test successful license key validation.
     */
    public function testValidationPassed()
    {
        $oResponse = new oxOnlineLicenseCheckResponse();
        $oResponse->code = 0;
        $oResponse->message = 'ACK';

        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->any())->method('doRequest')->will($this->returnValue($oResponse));

        $oUserCounter = $this->getMock('oxUserCounter', array('getAdminCount'), array(), '', false);
        $oUserCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue(25));
        /** @var oxUserCounter $oUserCounter */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller, $oUserCounter);

        $this->assertEquals(true, $oLicenseCheck->validate('validSerial'));

        return $oLicenseCheck;
    }

    /**
     * @depends testValidationPassed
     * @param oxOnlineLicenseCheck $oLicenseCheck
     */
    public function testErrorMessageEmptyOnSuccess($oLicenseCheck)
    {
        $this->assertEquals('', $oLicenseCheck->getErrorMessage());
    }

    /**
     * Test failed license key validation.
     */
    public function testValidationFailed()
    {
        $oResponse = new oxOnlineLicenseCheckResponse();
        $oResponse->code = 1;
        $oResponse->message = 'NACK';

        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->any())->method('doRequest')->will($this->returnValue($oResponse));
        /** @var oxOnlineLicenseCheckCaller $oCaller */

        $oUserCounter = $this->getMock('oxUserCounter', array('getAdminCount'), array(), '', false);
        $oUserCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue(25));
        /** @var oxUserCounter $oUserCounter */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller, $oUserCounter);

        $this->assertEquals(false, $oLicenseCheck->validate('invalidSerial'));

        return $oLicenseCheck;
    }

    /**
     * @depends testValidationFailed
     * @param oxOnlineLicenseCheck $oLicenseCheck
     */
    public function testErrorMessageSetOnFailure($oLicenseCheck)
    {
        $this->assertEquals(oxRegistry::getLang()->translateString('OLC_ERROR_SERIAL_NOT_VALID'), $oLicenseCheck->getErrorMessage());
    }

    public function testSerialsAreTakenFromConfigInShopSerialsValidation()
    {
        $iAdminUsers = 25;
        $iSubShops = 5;
        $aServers = array('7da43ed884a1ad1d6035d4c1d630fc4e' => array(
            'id' => '7da43ed884a1ad1d6035d4c1d630fc4e',
            'timestamp' => '1409911182',
            'ip' => null,
            'lastFrontendUsage' => '1409911182',
            'lastAdminUsage' => null,
        ));

        $oConfig = $this->getMock('oxConfig', array('getMandateCount'));
        $oConfig->expects($this->any())->method('getMandateCount')->will($this->returnValue($iSubShops));
        /** @var oxConfig $oConfig */
        $oConfig->setConfigParam('aServersData', $aServers);
        $this->setConfigParam('aServersData', $aServers);
        oxRegistry::set('oxConfig', $oConfig);

        $oRequest = new oxOnlineLicenseCheckRequest();
        $oRequest->edition = oxRegistry::getConfig()->getEdition();
        $oRequest->version = oxRegistry::getConfig()->getVersion();
        $oRequest->revision = oxRegistry::getConfig()->getRevision();
        $oRequest->shopUrl = oxRegistry::getConfig()->getShopUrl();
        $oRequest->pVersion = '1.0';
        $oRequest->productId = 'eShop';
        $oRequest->keys = new stdClass();
        $oRequest->keys->key = array('key1', 'key2');

        $oServers = new stdClass();
        $oServers->server = $aServers;
        $oRequest->productSpecificInformation = new stdClass();
        $oRequest->productSpecificInformation->servers = $oServers;

        $oCounter = new stdClass();
        $oCounter->name = 'admin users';
        $oCounter->value = $iAdminUsers;

        $oSubShops = new stdClass();
        $oSubShops->name = 'subShops';
        $oSubShops->value = $iSubShops;

        $oCounters = new stdClass();
        $oCounters->counter = array($oCounter, $oSubShops);
        $oRequest->productSpecificInformation->counters = $oCounters;

        $this->getConfig()->setConfigParam("aSerials", array('key1', 'key2'));

        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->once())->method('doRequest')->with($oRequest);
        /** @var oxOnlineLicenseCheckCaller $oUserCounter */

        $oUserCounter = $this->getMock('oxUserCounter', array('getAdminCount'), array(), '', false);
        $oUserCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue(25));
        /** @var oxUserCounter $oUserCounter */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller, $oUserCounter);
        $oLicenseCheck->validateShopSerials();
    }


    public function testIsExceptionWhenExceptionWasThrown()
    {
        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->any())->method('doRequest')->will($this->throwException(new oxException()));
        /** @var oxOnlineLicenseCheckCaller $oUserCounter */

        $oUserCounter = $this->getMock('oxUserCounter', array('getAdminCount'), array(), '', false);
        $oUserCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue(25));
        /** @var oxUserCounter $oUserCounter */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller, $oUserCounter);
        $oLicenseCheck->validate('validSerial');

        $this->assertEquals(true, $oLicenseCheck->isException());
    }

    public function testLog()
    {
        $oResponse = new oxOnlineLicenseCheckResponse();
        $oResponse->code = 0;
        $oResponse->message = 'ACK';

        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->any())->method('doRequest')->will($this->returnValue($oResponse));
        /** @var oxOnlineLicenseCheckCaller $oUserCounter */

        $oUserCounter = $this->getMock('oxUserCounter', array('getAdminCount'), array(), '', false);
        $oUserCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue(25));
        /** @var oxUserCounter $oUserCounter */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller, $oUserCounter);

        $this->setTime(10);

        $oLicenseCheck->validate('validSerial');

        $this->assertEquals(10, oxRegistry::getConfig()->getConfigParam('iOlcSuccess'));
    }
}