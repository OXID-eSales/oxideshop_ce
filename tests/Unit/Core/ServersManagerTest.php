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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxRegistry;

/**
 * @covers oxServersManager
 */
class ServersManagerTest extends \OxidTestCase
{

    public function tearDown()
    {
        parent::tearDown();
    }

    public function testGettingExistingServerByServerId()
    {
        $aServers = array('serverNameHash1' => array('timestamp' => 'timestamp'));
        $this->getConfig()->setConfigParam('aServersData', $aServers);

        $oExpectedServer = oxNew('oxApplicationServer');
        $oExpectedServer->setId('serverNameHash1');
        $oExpectedServer->setTimestamp('timestamp');

        $oServerList = oxNew('oxServersManager');
        $this->assertEquals($oExpectedServer, $oServerList->getServer('serverNameHash1'));
    }

    public function testGettingExistingServerByServerIdWhenMultipleServersExists()
    {
        $aServers = array(
            'serverNameHash1' => array('timestamp' => 'timestamp1'),
            'serverNameHash2' => array('timestamp' => 'timestamp2'),
            'serverNameHash3' => array('timestamp' => 'timestamp3'),
        );
        $this->getConfig()->setConfigParam('aServersData', $aServers);

        $oExpectedServer = oxNew('oxApplicationServer');
        $oExpectedServer->setId('serverNameHash2');
        $oExpectedServer->setTimestamp('timestamp2');

        $oServerList = oxNew('oxServersManager');
        $this->assertEquals($oExpectedServer, $oServerList->getServer('serverNameHash2'));
    }

    public function testGettingNotExistingServerByServerId()
    {
        $this->getConfig()->setConfigParam('aServersData', null);

        $oExpectedServer = oxNew('oxApplicationServer');
        $oExpectedServer->setId('serverNameHash1');

        $oServerList = oxNew('oxServersManager');
        $this->assertEquals($oExpectedServer, $oServerList->getServer('serverNameHash1'));
    }

    public function testServerSavingWhenNoServersExists()
    {
        $this->getConfig()->setConfigParam('aServersData', null);

        $oServer = oxNew('oxApplicationServer');
        $oServer->setId('serverNameHash1');
        $oServer->setTimestamp('timestamp');
        $oServer->setIp('127.0.0.1');
        $oServer->setLastFrontendUsage('frontendUsageTimestamp');
        $oServer->setLastAdminUsage('adminUsageTimestamp');
        $oServer->setIsValid();


        $oServerList = oxNew('oxServersManager');
        $oServerList->saveServer($oServer);

        $aExpectedServerData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => 'timestamp',
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestamp',
                'lastAdminUsage'    => 'adminUsageTimestamp',
                'isValid'           => true
            ),
        );
        $this->assertEquals($aExpectedServerData, $this->getConfig()->getConfigParam('aServersData'));
    }

    public function testUpdatingServer()
    {
        $this->getConfig()->setConfigParam(
            'aServersData', array(
                                 'serverNameHash1' => array(),
                                 'serverNameHash2' => array(
                                     'id'                => 'serverNameHash2',
                                     'timestamp'         => 'timestamp',
                                     'ip'                => '127.0.0.1',
                                     'lastFrontendUsage' => 'frontendUsageTimestamp',
                                     'lastAdminUsage'    => 'adminUsageTimestamp',
                                     'isValid'           => false
                                 ),
                                 'serverNameHash3' => array(),
                            )
        );

        $oServer = oxNew('oxApplicationServer');
        $oServer->setId('serverNameHash2');
        $oServer->setTimeStamp('timestampUpdated');
        $oServer->setIp('127.0.0.255');
        $oServer->setLastFrontendUsage('frontendUsageTimestampUpdated');
        $oServer->setLastAdminUsage('adminUsageTimestampUpdated');
        $oServer->setIsValid();

        $oServerList = oxNew('oxServersManager');
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
        $this->assertEquals($aExpectedServerData, $this->getConfig()->getConfigParam('aServersData'));
    }

    public function testUpdatingEmptyServer()
    {
        $this->getConfig()->setConfigParam(
            'aServersData', array(
                                 'serverNameHash1' => array(),
                            )
        );

        $oServer = oxNew('oxApplicationServer');
        $oServer->setId('serverNameHash1');
        $oServer->setTimeStamp('timestampUpdated');
        $oServer->setIp('127.0.0.1');
        $oServer->setLastFrontendUsage('frontendUsageTimestampUpdated');
        $oServer->setLastAdminUsage('adminUsageTimestampUpdated');
        $oServer->setIsValid(false);

        $oServerList = oxNew('oxServersManager');
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
        $this->assertEquals($aExpectedServerData, $this->getConfig()->getConfigParam('aServersData'));
    }

    public function testGetServerNodes()
    {
        $iCurrentTime = 1400000000;
        $this->setTime($iCurrentTime);

        $aStoredData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => $iCurrentTime,
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => false
            ),
            'serverNameHash2' => array(
                'id'                => 'serverNameHash2',
                'timestamp'         => $iCurrentTime,
                'ip'                => '127.0.0.2',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
        );

        $aServers = array(
            array(
                'id'                => 'serverNameHash2',
                'ip'                => '127.0.0.2',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
            ),
        );

        $this->getConfig()->setConfigParam('aServersData', $aStoredData);

        $oManager = oxNew('oxServersManager');

        $this->assertEquals($aServers, $oManager->getServers());
    }

    public function testDeleteServer()
    {
        $aStoredData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => 'timestampUpdated',
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => false
            ),
            'serverNameHash2' => array(
                'id'                => 'serverNameHash2',
                'timestamp'         => 'timestampUpdated',
                'ip'                => '127.0.0.2',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
        );

        $this->getConfig()->setConfigParam('aServersData', $aStoredData);

        $this->assertSame(2, count($this->getConfig()->getConfigParam('aServersData')));

        $oManager = oxNew('oxServersManager');
        $oManager->deleteServer('serverNameHash1');

        $this->assertSame(1, count($this->getConfig()->getConfigParam('aServersData')));
    }

    public function testMarkInactive()
    {
        $iCurrentTime = 1400000000;
        $this->setTime($iCurrentTime);

        $aStoredData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => $iCurrentTime - (11 * 3600),
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
            'serverNameHash2' => array(
                'id'                => 'serverNameHash2',
                'timestamp'         => $iCurrentTime - (25 * 3600),
                'ip'                => '127.0.0.2',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
        );

        $this->getConfig()->setConfigParam('aServersData', $aStoredData);

        $oManager = oxNew('oxServersManager');
        $oManager->markInActiveServers();

        $this->assertSame(1, count($oManager->getServers()));
    }

    public function testMarkInactiveNothingToMark()
    {
        $iCurrentTime = 1400000000;
        $this->setTime($iCurrentTime);

        $aStoredData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => $iCurrentTime - (11 * 3600),
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
            'serverNameHash2' => array(
                'id'                => 'serverNameHash2',
                'timestamp'         => $iCurrentTime - (15 * 3600),
                'ip'                => '127.0.0.2',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
        );

        $this->getConfig()->setConfigParam('aServersData', $aStoredData);

        $oManager = oxNew('oxServersManager');
        $oManager->markInActiveServers();

        $this->assertSame(2, count($oManager->getServers()));
    }

    public function testDeleteInactiveNoWhatToDelete()
    {
        $iCurrentTime = 1400000000;
        $this->setTime($iCurrentTime);

        $aStoredData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => $iCurrentTime - (11 * 3600),
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
            'serverNameHash2' => array(
                'id'                => 'serverNameHash2',
                'timestamp'         => $iCurrentTime - (25 * 3600),
                'ip'                => '127.0.0.2',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => false
            ),
        );

        $this->getConfig()->setConfigParam('aServersData', $aStoredData);

        $oManager = oxNew('oxServersManager');
        $oManager->deleteInActiveServers();

        $this->assertSame(2, count($this->getConfig()->getConfigParam('aServersData')));
    }

    public function testDeleteInactive()
    {
        $iCurrentTime = 1400000000;
        $this->setTime($iCurrentTime);

        $aStoredData = array(
            'serverNameHash1' => array(
                'id'                => 'serverNameHash1',
                'timestamp'         => $iCurrentTime - (11 * 3600),
                'ip'                => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => true
            ),
            'serverNameHash2' => array(
                'id'                => 'serverNameHash2',
                'timestamp'         => $iCurrentTime - (73 * 3600),
                'ip'                => '127.0.0.2',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage'    => 'adminUsageTimestampUpdated',
                'isValid'           => false
            ),
        );

        $this->getConfig()->setConfigParam('aServersData', $aStoredData);

        $oManager = oxNew('oxServersManager');
        $oManager->deleteInActiveServers();

        $this->assertSame(1, count($this->getConfig()->getConfigParam('aServersData')));
    }
}
