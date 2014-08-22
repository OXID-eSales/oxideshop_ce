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
        $aExpectedFrontendServerNodesData = array(
            $sServerId => array(
                'timestamp' => $sCurrentTime,
                'serverIp' => $sServerIp,
                'lastFrontendUsage' => $sCurrentTime,
                'lastAdminUsage' => '',
            ),
        );
        $aExpectedAdminServerNodesData = array(
            $sServerId => array(
                'timestamp' => $sCurrentTime,
                'serverIp' => $sServerIp,
                'lastFrontendUsage' => '',
                'lastAdminUsage' => $sCurrentTime,
            ),
        );

        return array(
            array(false, $aExpectedFrontendServerNodesData),
            array(true, $aExpectedAdminServerNodesData),
        );
    }

    /**
     * @param bool $blIsAdmin
     * @param array $aExpectedServerNodesData
     *
     * @dataProvider providerFrontendServerFirstAccess
     */
    public function testFrontendServerFirstAccess($blIsAdmin, $aExpectedServerNodesData)
    {
        $this->setAdminMode($blIsAdmin);

        $sServerId = $this->_sServerId;
        $sServerIp = $aExpectedServerNodesData[$sServerId]['serverIp'];

        $oUtilsDateMock = $this->getMock('oxUtilsDate', array('getTime'));
        $oUtilsDateMock->expects($this->any())->method('getTime')->will($this->returnValue($aExpectedServerNodesData[$sServerId]['timestamp']));
        /** @var oxUtilsDate $oUtilsDate */
        $oUtilsDate = $oUtilsDateMock;

        $oUtilsServerMock = $this->getMock('oxUtilsServer', array('getServerNodeId', 'getServerIp'));
        $oUtilsServerMock->expects($this->any())->method('getServerNodeId')->will($this->returnValue($sServerId));
        $oUtilsServerMock->expects($this->any())->method('getServerIp')->will($this->returnValue($sServerIp));
        /** @var oxUtilsServer $oUtilsDate */
        $oUtilsServer = $oUtilsServerMock;

        $this->getConfig()->saveShopConfVar('arr', 'aServerNodesData', null);

        $oServerNodeProcessor = new oxServerNodeProcessor(null, null, $oUtilsServer, $oUtilsDate);
        $oServerNodeProcessor->process();
        $aServerNodesData = $this->getConfigParam('aServerNodesData');

        $this->assertEquals($aExpectedServerNodesData, $aServerNodesData);
    }
}