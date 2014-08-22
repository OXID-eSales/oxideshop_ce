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
    /** @var oxUtilsDate  */
    private $_oUtilsDate;

    public function setUp()
    {
        parent::setUp();
        $this->_oUtilsDate = oxRegistry::get('oxUtilsDate');
    }

    public function tearDown()
    {
        oxRegistry::set('oxUtilsDate', $this->_oUtilsDate);
        parent::tearDown();
    }

    public function testFrontendServerDoesNotExist()
    {
        $aExpectedServerNodesData = array(
            '0' => array(
                'timestamp' => time(),
                'serverId' => '172.168.1.50',
                'lastFrontendUsage' => '',
                'lastAdminUsage' => '',
            ),
        );
        $this->mockServerInformation($aExpectedServerNodesData);

        $oServerNodeProcessor = new oxServerNodeProcessor();
        $oServerNodeProcessor->process();
        $aServerNodesData = $this->getConfigParam('aServerNodesData');

        $this->assertServerInformationSame($aExpectedServerNodesData, $aServerNodesData);
    }

    private function assertServerInformationSame($aExpectedServerNodesData, $aActualServerNodesData)
    {
        sort($aExpectedServerNodesData);
        sort($aActualServerNodesData);

        $this->assertEquals($aExpectedServerNodesData, $aActualServerNodesData);
    }

    private function mockServerInformation($aServerInformation)
    {
        $oUtilsDate = $this->getMock('oxUtilsDate', array('getTime'));
        $oUtilsDate->expects($this->any())->method('getTime')->will($this->returnValue($aServerInformation['0']['timestamp']));
        /** @var oxUtilsDate $oUtils */
        oxRegistry::set('oxUtilsDate', $oUtilsDate);
    }
}