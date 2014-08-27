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
 * Manages application servers information.
 *
 * @internal Do not make a module extension for this class.
 * @see http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxServersManager
{

    /**
     * Servers data array.
     * @var array
     */
    private $_aServersData = array();

    /**
     * Initiates servers array with content from configuration.
     */
    public function __construct()
    {
        $this->_aServersData = (array) oxRegistry::getConfig()->getConfigParam('aServersData');
    }

    /**
     * Returns server based on server id.
     *
     * @param string $sNodeId
     * @return oxApplicationServer
     */
    public function getServer($sNodeId)
    {
        $aNodeData = $this->_getServerData($sNodeId);
        return $this->_createServer($sNodeId, $aNodeData);
    }

    /**
     * Saves given server information to config.
     *
     * @param oxApplicationServer $oNode
     */
    public function saveServer($oNode)
    {
        $aServersData = $this->_getServersData();
        $aServersData[$oNode->getId()] = array(
            'id' => $oNode->getId(),
            'timestamp' => $oNode->getTimestamp(),
            'ip' => $oNode->getIp(),
            'lastFrontendUsage' => $oNode->getLastFrontendUsage(),
            'lastAdminUsage' => $oNode->getLastAdminUsage(),
        );

        oxRegistry::getConfig()->setConfigParam('aServersData', $aServersData);
    }

    /**
     * Returns servers information array.
     *
     * @return array
     */
    protected function _getServersData()
    {
        return $this->_aServersData;
    }

    /**
     * Returns server information from configuration.
     *
     * @param string $sId
     * @return array
     */
    protected function _getServerData($sId)
    {
        $aNodes = $this->_getServersData();
        return array_key_exists($sId, $aNodes) ? $aNodes[$sId] : array();
    }

    /**
     * Creates oxApplicationServer from given server id and data.
     *
     * @param string $sServerId
     * @param array $aData
     * @return oxApplicationServer
     */
    protected function _createServer($sServerId, $aData = array())
    {
        /** @var oxApplicationServer $oAppServer */
        $oAppServer = oxNew('oxApplicationServer');

        $oAppServer->setId($sServerId);
        $oAppServer->setTimestamp($this->_getServerParameter($aData, 'timestamp'));
        $oAppServer->setIp($this->_getServerParameter($aData, 'serverIp'));
        $oAppServer->setLastFrontendUsage($this->_getServerParameter($aData, 'lastFrontendUsage'));
        $oAppServer->setLastAdminUsage($this->_getServerParameter($aData, 'lastAdminUsage'));

        return $oAppServer;
    }

    /**
     *
     *
     * @param $aData
     * @param $sName
     * @return mixed
     */
    protected function _getServerParameter($aData, $sName)
    {
        return array_key_exists($sName, $aData)? $aData[$sName] : null;
    }

}