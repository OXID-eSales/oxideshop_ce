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

/**
 * @covers oxServersManager
 */
class Unit_Core_oxServersManagerTest extends OxidTestCase
{

    public function testGettingExistingNodeByServerIpAddress()
    {
        $aNodes = array('serverNameHash1' => array('timestamp' => 'timestamp'));
        $this->getConfig()->setConfigParam('aServerNodesData', $aNodes);

        $oExpectedNode = new oxApplicationServer();
        $oExpectedNode->setId('serverNameHash1');
        $oExpectedNode->setTimestamp('timestamp');

        $oNodeList = new oxServersManager();
        $this->assertEquals($oExpectedNode, $oNodeList->getNode('serverNameHash1'));
    }

    public function testGettingExistingNodeByServerIpAddressWhenMultipleNodesExists()
    {
        $aNodes = array(
            'serverNameHash1' => array('timestamp' => 'timestamp1'),
            'serverNameHash2' => array('timestamp' => 'timestamp2'),
            'serverNameHash3' => array('timestamp' => 'timestamp3'),
        );
        $this->getConfig()->setConfigParam('aServerNodesData', $aNodes);

        $oExpectedNode = new oxApplicationServer();
        $oExpectedNode->setId('serverNameHash2');
        $oExpectedNode->setTimestamp('timestamp2');

        $oNodeList = new oxServersManager();
        $this->assertEquals($oExpectedNode, $oNodeList->getNode('serverNameHash2'));
    }

    public function testGettingNotExistingNodeByServerIpAddress()
    {
        $this->getConfig()->setConfigParam('aServerNodesData', null);

        $oExpectedNode = new oxApplicationServer();
        $oExpectedNode->setId('serverNameHash1');

        $oNodeList = new oxServersManager();
        $this->assertEquals($oExpectedNode, $oNodeList->getNode('serverNameHash1'));
    }

    public function testNodeSavingWhenNoNodesExists()
    {
        oxRegistry::getConfig()->setConfigParam('aServerNodesData', null);

        $oNode = new oxApplicationServer();
        $oNode->setId('serverNameHash1');
        $oNode->setTimestamp('timestamp');
        $oNode->setIp('127.0.0.1');
        $oNode->setLastFrontendUsage('frontendUsageTimestamp');
        $oNode->setLastAdminUsage('adminUsageTimestamp');

        $oNodeList = new oxServersManager();
        $oNodeList->saveNode($oNode);

        $aExpectedNodeData = array(
            'serverNameHash1' => array(
                'timestamp' => 'timestamp',
                'serverIp' => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestamp',
                'lastAdminUsage' => 'adminUsageTimestamp',
            ),
        );
        $this->assertEquals($aExpectedNodeData, $this->getConfig()->getConfigParam('aServerNodesData'));
    }

    public function testUpdatingNode()
    {
        oxRegistry::getConfig()->setConfigParam('aServerNodesData', array(
            'serverNameHash1' => array(),
            'serverNameHash2' => array(
                'timestamp' => 'timestamp',
                'serverIp' => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestamp',
                'lastAdminUsage' => 'adminUsageTimestamp',
            ),
            'serverNameHash3' => array(),
        ));

        $oNode = new oxApplicationServer();
        $oNode->setId('serverNameHash2');
        $oNode->setTimeStamp('timestampUpdated');
        $oNode->setIp('127.0.0.255');
        $oNode->setLastFrontendUsage('frontendUsageTimestampUpdated');
        $oNode->setLastAdminUsage('adminUsageTimestampUpdated');

        $oNodeList = new oxServersManager();
        $oNodeList->saveNode($oNode);

        $aExpectedNodeData = array(
            'serverNameHash1' => array(),
            'serverNameHash2' => array(
                'timestamp' => 'timestampUpdated',
                'serverIp' => '127.0.0.255',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage' => 'adminUsageTimestampUpdated',
            ),
            'serverNameHash3' => array(),
        );
        $this->assertEquals($aExpectedNodeData, $this->getConfig()->getConfigParam('aServerNodesData'));
    }

    public function testUpdatingEmptyNode()
    {
        oxRegistry::getConfig()->setConfigParam('aServerNodesData', array(
            'serverNameHash1' => array(),
        ));

        $oNode = new oxApplicationServer();
        $oNode->setId('serverNameHash1');
        $oNode->setTimeStamp('timestampUpdated');
        $oNode->setIp('127.0.0.1');
        $oNode->setLastFrontendUsage('frontendUsageTimestampUpdated');
        $oNode->setLastAdminUsage('adminUsageTimestampUpdated');

        $oNodeList = new oxServersManager();
        $oNodeList->saveNode($oNode);

        $aExpectedNodeData = array(
            'serverNameHash1' => array(
                'timestamp' => 'timestampUpdated',
                'serverIp' => '127.0.0.1',
                'lastFrontendUsage' => 'frontendUsageTimestampUpdated',
                'lastAdminUsage' => 'adminUsageTimestampUpdated',
            ),
        );
        $this->assertEquals($aExpectedNodeData, $this->getConfig()->getConfigParam('aServerNodesData'));
    }
}