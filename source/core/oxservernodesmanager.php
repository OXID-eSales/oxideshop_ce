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
 * oxServerNodesManager
 *
 * @internal Do not make a module extension for this class.
 * @see http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxServerNodesManager
{

    /**
     * Nodes data array.
     * @var array
     */
    private $_aNodesData = array();

    /**
     * Initiates nodes array with content from configuration.
     */
    public function __construct()
    {
        $this->_aNodesData = (array) oxRegistry::getConfig()->getConfigParam('aServerNodesData');
    }

    /**
     * Returns not based on server ip address.
     *
     * @param string $sNodeId
     * @return oxServerNode
     */
    public function getNode($sNodeId)
    {
        $aNodeData = $this->_getNodeData($sNodeId);
        return $this->_createNode($sNodeId, $aNodeData);
    }

    /**
     * @param oxServerNode $oNode
     */
    public function saveNode($oNode)
    {
        $aNodes = $this->_getNodesData();
        $aNodes[$oNode->getId()] = array(
            'timestamp' => $oNode->getTimestamp(),
            'serverIp' => $oNode->getIp(),
            'lastFrontendUsage' => $oNode->getLastFrontendUsage(),
            'lastAdminUsage' => $oNode->getLastAdminUsage(),
        );

        oxRegistry::getConfig()->setConfigParam('aServerNodesData', $aNodes);
    }

    /**
     * Returns server nodes information array.
     *
     * @return array
     */
    protected function _getNodesData()
    {
        return $this->_aNodesData;
    }

    /**
     * @param $sIpAddress
     * @return array
     */
    protected function _getNodeData($sIpAddress)
    {
        $aNodes = $this->_getNodesData();
        return array_key_exists($sIpAddress, $aNodes) ? $aNodes[$sIpAddress] : array();
    }

    /**
     * Creates oxServerNode from given ip address and data.
     *
     * @param $sNodeId
     * @param $aData
     * @return oxServerNode
     */
    protected function _createNode($sNodeId, $aData = array())
    {
        /** @var oxServerNode $oNode */
        $oNode = oxNew('oxServerNode');

        $oNode->setId($sNodeId);
        $oNode->setTimestamp($this->_getNodeParameter($aData, 'timestamp'));
        $oNode->setIp($this->_getNodeParameter($aData, 'serverIp'));
        $oNode->setLastFrontendUsage($this->_getNodeParameter($aData, 'lastFrontendUsage'));
        $oNode->setLastAdminUsage($this->_getNodeParameter($aData, 'lastAdminUsage'));

        return $oNode;
    }

    /**
     * @param $aData
     * @param $sName
     * @return mixed
     */
    protected function _getNodeParameter($aData, $sName)
    {
        return array_key_exists($sName, $aData)? $aData[$sName] : null;
    }

}