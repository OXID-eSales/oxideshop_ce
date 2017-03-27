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
 * Class Integration_OnlineInfo_FrontendServersInformationStoringTest
 *
 * @covers oxServerProcessor
 * @covers oxApplicationServer
 * @covers oxServerChecker
 * @covers oxServerManager
 */
class Integration_OnlineInfo_FrontendServersInformationStoringTest extends OxidTestCase
{

    /** @var string server id. */
    private $_sServerId = '7da43ed884a1zd1d6035d4c1d630fc4e';

    /**
     * @return array
     */
    public function providerFrontendServerFirstAccess()
    {
        $sServerId = $this->_sServerId;
        $sServerIp = '192.168.0.5';
        $sCurrentTime = time();
        $aExpectedFrontendServersData = array(
            'id'                => $sServerId,
            'timestamp'         => $sCurrentTime,
            'ip'                => $sServerIp,
            'lastFrontendUsage' => $sCurrentTime,
            'lastAdminUsage'    => '',
            'isValid'           => true,
        );
        $aExpectedAdminServersData = array(
            'id'                => $sServerId,
            'timestamp'         => $sCurrentTime,
            'ip'                => $sServerIp,
            'lastFrontendUsage' => '',
            'lastAdminUsage'    => $sCurrentTime,
            'isValid'           => true,
        );

        return array(
            array(false, $aExpectedFrontendServersData),
            array(true, $aExpectedAdminServersData),
        );
    }

    /**
     * @param bool  $blIsAdmin
     * @param array $aExpectedServersData
     *
     * @dataProvider providerFrontendServerFirstAccess
     */
    public function testFrontendServerFirstAccess($blIsAdmin, $aExpectedServersData)
    {
        $sServerId = $this->_sServerId;
        $sServerIp = $aExpectedServersData['ip'];
        $this->setAdminMode($blIsAdmin);
        $oUtilsDate = $this->_createDateMock($aExpectedServersData);
        $oUtilsServer = $this->_createServerMock($sServerId, $sServerIp);

        $this->getConfig()->saveSystemConfigParameter('arr', 'aServersData_'.$sServerId, null);

        $oServerProcessor = new oxServerProcessor(new oxServersManager(), new oxServerChecker(), $oUtilsServer, $oUtilsDate);
        $oServerProcessor->process();
        $aServersData = $this->getConfig()->getSystemConfigParameter('aServersData_'.$sServerId);

        $this->assertEquals($aExpectedServersData, $aServersData);
    }

    /**
     * @param $aExpectedServersData
     *
     * @return oxUtilsDate
     */
    private function _createDateMock($aExpectedServersData)
    {
        $oUtilsDate = $this->getMock('oxUtilsDate', array('getTime'));
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
        $oUtilsServer = $this->getMock('oxUtilsServer', array('getServerNodeId', 'getServerIp'));
        $oUtilsServer->expects($this->any())->method('getServerNodeId')->will($this->returnValue($sServerId));
        $oUtilsServer->expects($this->any())->method('getServerIp')->will($this->returnValue($sServerIp));

        return $oUtilsServer;
    }
}