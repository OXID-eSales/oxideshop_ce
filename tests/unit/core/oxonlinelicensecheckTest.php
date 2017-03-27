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

class Unit_Core_oxOnlineLicenseCheckTest extends OxidTestCase
{
    public function testRequestFormation()
    {
        $iAdminUsers = 25;
        $iActiveAdminUsers = 10;
        $iSubShops = 5;
        $aServers = array('7da43ed884a1ad1d6035d4c1d630fc4e' => array(
            'id' => '7da43ed884a1ad1d6035d4c1d630fc4e',
            'timestamp' => '1409911182',
            'ip' => null,
            'lastFrontendUsage' => '1409911182',
            'lastAdminUsage' => null,
        ));
        $aCounters = array(
            array(
                'name' => 'admin users',
                'value' => $iAdminUsers,
            ),
            array(
                'name' => 'active admin users',
                'value' => $iActiveAdminUsers,
            ),
            array(
                'name' => 'subShops',
                'value' => $iSubShops,
            )
        );

        $oConfig = $this->getMock('oxConfig', array('getMandateCount'));
        $oConfig->expects($this->any())->method('getMandateCount')->will($this->returnValue($iSubShops));

        /** @var oxConfig $oConfig */
        oxRegistry::set('oxConfig', $oConfig);

        $oRequest = new oxOnlineLicenseCheckRequest();
        $oRequest->revision = oxRegistry::getConfig()->getRevision();
        $oRequest->pVersion = '1.1';
        $oRequest->productId = 'eShop';
        $oRequest->keys = array('key' => array('validSerial'));
        $oRequest->productSpecificInformation = new stdClass();
        $oRequest->productSpecificInformation->servers = array('server' => $aServers);
        $oRequest->productSpecificInformation->counters = array('counter' => $aCounters);

        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->once())->method('doRequest')->with($oRequest);
        /** @var oxOnlineLicenseCheckCaller $oCaller */

        $oUserCounter = $this->getMock('oxUserCounter', array('getAdminCount', 'getActiveAdminCount'), array(), '', false);
        $oUserCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue($iAdminUsers));
        $oUserCounter->expects($this->once())->method('getActiveAdminCount')->will($this->returnValue($iActiveAdminUsers));
        /** @var oxUserCounter $oUserCounter */

        $oServersManager = $this->getMock('oxServersManager', array('getServers'), array(), '', false);
        $oServersManager->expects($this->once())->method('getServers')->will($this->returnValue($aServers));
        /** @var oxServersManager $oServersManager */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller);
        $oLicenseCheck->setServersManager($oServersManager);
        $oLicenseCheck->setUserCounter($oUserCounter);

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

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller);

        $this->assertEquals(true, $oLicenseCheck->validate('validSerial'));

        return $oLicenseCheck;
    }

    /**
     * @depends testValidationPassed
     *
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

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller);

        $this->assertEquals(false, $oLicenseCheck->validate('invalidSerial'));

        return $oLicenseCheck;
    }

    /**
     * @depends testValidationFailed
     *
     * @param oxOnlineLicenseCheck $oLicenseCheck
     */
    public function testErrorMessageSetOnFailure($oLicenseCheck)
    {
        $this->assertEquals(oxRegistry::getLang()->translateString('OLC_ERROR_SERIAL_NOT_VALID'), $oLicenseCheck->getErrorMessage());
    }

    public function testSerialsAreTakenFromConfigInShopSerialsValidation()
    {
        $iAdminUsers = 25;
        $iActiveAdminUsers = 10;
        $iSubShops = 5;
        $aServers = array('7da43ed884a1ad1d6035d4c1d630fc4e' => array(
            'id' => '7da43ed884a1ad1d6035d4c1d630fc4e',
            'timestamp' => '1409911182',
            'ip' => null,
            'lastFrontendUsage' => '1409911182',
            'lastAdminUsage' => null,
        ));
        $aCounters = array(
            array(
                'name' => 'admin users',
                'value' => $iAdminUsers,
            ),
            array(
                'name' => 'active admin users',
                'value' => $iActiveAdminUsers,
            ),
            array(
                'name' => 'subShops',
                'value' => $iSubShops,
            )
        );

        $oConfig = $this->getMock('oxConfig', array('getMandateCount'));
        $oConfig->expects($this->any())->method('getMandateCount')->will($this->returnValue($iSubShops));
        /** @var oxConfig $oConfig */
        oxRegistry::set('oxConfig', $oConfig);

        $oRequest = new oxOnlineLicenseCheckRequest();
        $oRequest->edition = oxRegistry::getConfig()->getEdition();
        $oRequest->version = oxRegistry::getConfig()->getVersion();
        $oRequest->revision = oxRegistry::getConfig()->getRevision();
        $oRequest->shopUrl = oxRegistry::getConfig()->getShopUrl();
        $oRequest->pVersion = '1.1';
        $oRequest->productId = 'eShop';
        $oRequest->keys = array('key' => array('key1', 'key2'));

        $oRequest->productSpecificInformation = new stdClass();
        $oRequest->productSpecificInformation->servers = array('server' => $aServers);
        $oRequest->productSpecificInformation->counters = array('counter' => $aCounters);

        $this->getConfig()->setConfigParam("aSerials", array('key1', 'key2'));

        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->once())->method('doRequest')->with($oRequest);
        /** @var oxOnlineLicenseCheckCaller $oUserCounter */

        $oUserCounter = $this->getMock('oxUserCounter', array('getAdminCount', 'getActiveAdminCount'), array(), '', false);
        $oUserCounter->expects($this->once())->method('getAdminCount')->will($this->returnValue($iAdminUsers));
        $oUserCounter->expects($this->once())->method('getActiveAdminCount')->will($this->returnValue($iActiveAdminUsers));
        /** @var oxUserCounter $oUserCounter */

        $oServersManager = $this->getMock('oxServersManager', array('getServers'), array(), '', false);
        $oServersManager->expects($this->once())->method('getServers')->will($this->returnValue($aServers));
        /** @var oxServersManager $oServersManager */


        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller);
        $oLicenseCheck->setServersManager($oServersManager);
        $oLicenseCheck->setUserCounter($oUserCounter);
        $oLicenseCheck->validateShopSerials();
    }

    public function testNewSerialIsAddedToExistingSerials()
    {
        $iSubShops = 5;
        $aCounters = array(array(
            'name' => 'subShops',
            'value' => $iSubShops,
        ));

        oxDb::getDb()->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");

        $oConfig = $this->getMock('oxConfig', array('getMandateCount'));
        $oConfig->expects($this->any())->method('getMandateCount')->will($this->returnValue($iSubShops));
        /** @var oxConfig $oConfig */
        oxRegistry::set('oxConfig', $oConfig);

        $oRequest = new oxOnlineLicenseCheckRequest();
        $oRequest->edition = oxRegistry::getConfig()->getEdition();
        $oRequest->version = oxRegistry::getConfig()->getVersion();
        $oRequest->revision = oxRegistry::getConfig()->getRevision();
        $oRequest->shopUrl = oxRegistry::getConfig()->getShopUrl();
        $oRequest->pVersion = '1.1';
        $oRequest->productId = 'eShop';
        $aKeys = array('key1', 'key2', array('attributes' => array('state' => 'new'), 'value' => 'new_serial'));
        $oRequest->keys = array('key' => $aKeys);

        $oRequest->productSpecificInformation = new stdClass();
        $oRequest->productSpecificInformation->counters = array('counter' => $aCounters);

        $this->getConfig()->setConfigParam("aSerials", array('key1', 'key2'));

        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->once())->method('doRequest')->with($oRequest);
        /** @var oxOnlineLicenseCheckCaller $oUserCounter */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller);
        $oLicenseCheck->validateNewSerial('new_serial');
    }


    public function testIsExceptionWhenExceptionWasThrown()
    {
        $oCaller = $this->getMock('oxOnlineLicenseCheckCaller', array('doRequest'), array(), '', false);
        $oCaller->expects($this->any())->method('doRequest')->will($this->throwException(new oxException()));
        /** @var oxOnlineLicenseCheckCaller $oUserCounter */

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller);
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

        $oLicenseCheck = new oxOnlineLicenseCheck($oCaller);

        $this->setTime(10);

        $oLicenseCheck->validate('validSerial');

        $this->assertEquals(10, oxRegistry::getConfig()->getConfigParam('iOlcSuccess'));
    }
}