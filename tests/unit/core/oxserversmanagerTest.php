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
 * @covers oxServersManager
 */
class Unit_Core_oxServersManagerTest extends OxidTestCase
{

    public function setUp()
    {
        parent::setUp();
        oxDb::getDb()->execute("DELETE FROM oxconfig WHERE oxvarname like 'aServersData_%'");
    }

    public function tearDown()
    {
        parent::tearDown();
        $oUtilsDate = new oxUtilsDate();
        oxRegistry::set('oxUtilsDate', $oUtilsDate);
    }

    public function testGettingExistingServerByServerId()
    {
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1',
            array(
                'id'        => 'serverNameHash1',
                'timestamp' => 'timestamp')
        );

        $oExpectedServer = new oxApplicationServer();
        $oExpectedServer->setId('serverNameHash1');
        $oExpectedServer->setTimestamp('timestamp');

        $oServerList = new oxServersManager();
        $this->assertEquals($oExpectedServer, $oServerList->getServer('serverNameHash1'));
    }

    public function testGettingExistingServerByServerIdWhenMultipleServersExists()
    {
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1',
            array('id' => 'serverNameHash1', 'timestamp' => 'timestamp1'));
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash2',
            array('id' => 'serverNameHash2', 'timestamp' => 'timestamp2'));
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash3',
            array('id' => 'serverNameHash3', 'timestamp' => 'timestamp3'));

        $oExpectedServer = new oxApplicationServer();
        $oExpectedServer->setId('serverNameHash2');
        $oExpectedServer->setTimestamp('timestamp2');

        $oServerList = new oxServersManager();
        $this->assertEquals($oExpectedServer, $oServerList->getServer('serverNameHash2'));
    }

    public function testGettingNotExistingServerByServerId()
    {
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', null);

        $oExpectedServer = new oxApplicationServer();
        $oExpectedServer->setId('serverNameHash1');

        $oServerList = new oxServersManager();
        $this->assertEquals($oExpectedServer, $oServerList->getServer('serverNameHash1'));
    }

    public function testServerSavingWhenNoServersExists()
    {
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', null);

        $oServer = new oxApplicationServer();
        $oServer->setId('serverNameHash1');
        $oServer->setTimestamp('timestamp');
        $oServer->setIp('127.0.0.1');
        $oServer->setLastFrontendUsage('frontendUsageTimestamp');
        $oServer->setLastAdminUsage('adminUsageTimestamp');
        $oServer->setIsValid();


        $oServerList = new oxServersManager();
        $oServerList->saveServer($oServer);

        $aExpectedServerData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => 'timestamp',
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestamp',
                'lastAdminUsage'    => 'adminUsageTimestamp',
                'isValid'           => true
        ));
        $this->assertEquals($aExpectedServerData, $oServerList->getServersData());
    }

    public function testUpdatingServer()
    {
        $oConfig = oxRegistry::getConfig();
        $oConfig->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', array());
        $oConfig->saveSystemConfigParameter('arr', 'aServersData_serverNameHash3', array());
        $oConfig->saveSystemConfigParameter(
            'arr',
            'aServersData_serverNameHash2',
            array(  'id'                => 'serverNameHash2',
                    'timestamp'         => 'timestamp',
                    'ip'                => '127.0.0.1',
                    'lastFrontendUsage' => 'frontendUsageTimestamp',
                    'lastAdminUsage'    => 'adminUsageTimestamp',
                    'isValid'           => false
            )
        );

        $oServer = new oxApplicationServer();
        $oServer->setId('serverNameHash2');
        $oServer->setTimeStamp('timestampUpdated');
        $oServer->setIp('127.0.0.255');
        $oServer->setLastFrontendUsage('frontendUsageTimestampUpdated');
        $oServer->setLastAdminUsage('adminUsageTimestampUpdated');
        $oServer->setIsValid();

        $oServerList = new oxServersManager();
        $oServerList->saveServer($oServer);

        $aExpectedServerData = array(
            'serverNameHash1' => array(),
            'serverNameHash2' => array(
                'id'                => 'serverNameHash2',
                'timestamp'         => 'timestampUpdated',
                'ip'                => '127.0.0.255',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
            'serverNameHash3' => array(),
        );

        $this->assertEquals($aExpectedServerData, $oServerList->getServersData());
    }

    public function testUpdatingEmptyServer()
    {
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', array());

        $oServer = new oxApplicationServer();
        $oServer->setId('serverNameHash1');
        $oServer->setTimeStamp('timestampUpdated');
        $oServer->setIp('127.0.0.1');
        $oServer->setLastFrontendUsage('frontendUsageTimestampUpdated');
        $oServer->setLastAdminUsage('adminUsageTimestampUpdated');
        $oServer->setIsValid(false);

        $oServerList = new oxServersManager();
        $oServerList->saveServer($oServer);

        $aExpectedServerData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => 'timestampUpdated',
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => false
            ),
        );
        $this->assertEquals($aExpectedServerData, $oServerList->getServersData());
    }

    public function testGetServerNodes()
    {
        $iCurrentTime = 1400000000;
        $this->_prepareCurrentTime($iCurrentTime);

        $this->_storeInitialServersData($iCurrentTime, $iCurrentTime);

        $aServers = array(
            array(
                'id'                => 'serverNameHash2',
                'ip'                => '127.0.0.2',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
            ),
        );

        $oManager = new oxServersManager();

        $this->assertEquals($aServers, $oManager->getServers());
    }

    public function testDeleteServer()
    {
        $this->_storeInitialServersData();

        $oManager = new oxServersManager();
        $aServersData = $oManager->getServersData();

        $this->assertNotNull($aServersData['serverNameHash1']);
        $this->assertNotNull($aServersData['serverNameHash2']);

        $oManager->deleteServer('serverNameHash1');

        $aServersData2 = $oManager->getServersData();

        $this->assertNull($aServersData2['serverNameHash1']);
        $this->assertNotNull($aServersData2['serverNameHash2']);
    }

    public function testMarkInactive()
    {
        $iCurrentTime = 1400000000;
        $this->_prepareCurrentTime($iCurrentTime);

        $this->_storeInitialServersData($iCurrentTime - (11 * 3600), $iCurrentTime - (25 * 3600), true);

        $oManager = new oxServersManager();

        $this->assertSame(1, count($oManager->getServers()));
    }

    public function testMarkInactiveNothingToMark()
    {
        $iCurrentTime = 1400000000;
        $this->_prepareCurrentTime($iCurrentTime);

        $this->_storeInitialServersData($iCurrentTime - (11 * 3600), $iCurrentTime - (15 * 3600), true, true);

        $oManager = new oxServersManager();

        $this->assertSame(2, count($oManager->getServers()));
    }

    public function testDeleteInactive()
    {
        $iCurrentTime = 1400000000;
        $this->_prepareCurrentTime($iCurrentTime);

        $this->_storeInitialServersData($iCurrentTime - (11 * 3600), $iCurrentTime - (73 * 3600), true, false);

        $oManager = new oxServersManager();

        $this->assertSame(1, count($oManager->getServers()));
    }

    private function _storeInitialServersData($iTimestamp1 = 'timestampUpdated', $iTimestamp2 = 'timestampUpdated', $blActiveServer1 = false, $blActiveServer2 = true)
    {
        $aStoredData1 = array(
            'id'                => 'serverNameHash1',
            'timestamp'         => $iTimestamp1,
            'ip'                => '127.0.0.1',
            'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
            'lastAdminUsage'    => 'adminUsageTimestampUpdated',
            'isValid'           => $blActiveServer1
        );
        $aStoredData2 = array(
            'id'                => 'serverNameHash2',
            'timestamp'         => $iTimestamp2,
            'ip'                => '127.0.0.2',
            'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
            'lastAdminUsage'    => 'adminUsageTimestampUpdated',
            'isValid'           => $blActiveServer2
        );
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash1', $aStoredData1);
        oxRegistry::getConfig()->saveSystemConfigParameter('arr', 'aServersData_serverNameHash2', $aStoredData2);

    }
    /**
     * @param int $iCurrentTime
     */
    private function _prepareCurrentTime($iCurrentTime)
    {
        $oUtilsDate = $this->getMock('oxUtilsDate', array('getTime'));
        $oUtilsDate->expects($this->any())->method('getTime')->will($this->returnValue($iCurrentTime));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);
    }

}