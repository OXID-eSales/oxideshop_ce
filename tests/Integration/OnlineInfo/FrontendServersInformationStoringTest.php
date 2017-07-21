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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\OnlineInfo;

use oxUtilsDate;
use oxUtilsServer;

/**
 * Class Integration_OnlineInfo_FrontendServersInformationStoringTest
 *
 * @covers \OxidEsales\Eshop\Core\Service\ApplicationServerService
 * @covers oxApplicationServer
 */
class FrontendServersInformationStoringTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /** @var string server id. */
    private $serverId = '7da43ed884a1zd1d6035d4c1d630fc4e';

    /**
     * @return array
     */
    public function providerFrontendServerFirstAccess()
    {
        $serverId = $this->serverId;
        $serverIp = '192.168.0.5';
        $currentTime = time();
        $expectedFrontendServersData = array(
            'id'                => $serverId,
            'timestamp'         => $currentTime,
            'ip'                => $serverIp,
            'lastFrontendUsage' => $currentTime,
            'lastAdminUsage'    => '',
            'isValid'           => null,
        );
        $expectedAdminServersData = array(
            'id'                => $serverId,
            'timestamp'         => $currentTime,
            'ip'                => $serverIp,
            'lastFrontendUsage' => '',
            'lastAdminUsage'    => $currentTime,
            'isValid'           => null,
        );

        return array(
            array(false, $expectedFrontendServersData),
            array(true, $expectedAdminServersData),
        );
    }

    /**
     * @param bool  $isAdmin
     * @param array $expectedServersData
     *
     * @dataProvider providerFrontendServerFirstAccess
     */
    public function testFrontendServerFirstAccess($isAdmin, $expectedServersData)
    {
        $serverId = $this->serverId;
        $serverIp = $expectedServersData['ip'];
        $utilsDate = $this->_createDateMock($expectedServersData);
        $utilsServer = $this->_createServerMock($serverId, $serverIp);

        $this->getConfig()->saveSystemConfigParameter('arr', 'aServersData_'.$serverId, null);

        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $databaseProvider = oxNew(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);

        /** @var \OxidEsales\Eshop\Core\Service\ApplicationServerService $oApplicationServerService */
        $oApplicationServerService = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class,
            $appServerDao,
            $utilsServer,
            $utilsDate->getTime()
        );

        $oApplicationServerService->updateAppServerInformation($isAdmin);
        $aServersData = $this->getConfig()->getSystemConfigParameter('aServersData_'.$serverId);

        $this->assertEquals($expectedServersData, $aServersData);
    }

    /**
     * @param $aExpectedServersData
     *
     * @return oxUtilsDate
     */
    private function _createDateMock($aExpectedServersData)
    {
        $oUtilsDate = $this->getMock(\OxidEsales\Eshop\Core\UtilsDate::class, array('getTime'));
        $oUtilsDate->expects($this->any())->method('getTime')->will($this->returnValue($aExpectedServersData['timestamp']));

        return $oUtilsDate;
    }

    /**
     * @param $sServerId
     * @param $sServerIp
     *
     * @return oxUtilsServer
     */
    private function _createServerMock($sServerId, $sServerIp)
    {
        $oUtilsServer = $this->getMock(\OxidEsales\Eshop\Core\UtilsServer::class, array('getServerNodeId', 'getServerIp'));
        $oUtilsServer->expects($this->any())->method('getServerNodeId')->will($this->returnValue($sServerId));
        $oUtilsServer->expects($this->any())->method('getServerIp')->will($this->returnValue($sServerIp));

        return $oUtilsServer;
    }
}
