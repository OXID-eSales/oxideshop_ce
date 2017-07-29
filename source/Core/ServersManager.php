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

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;

/**
 * Manages application servers information.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ServersManager
{

    /**
     * Time in seconds, server node information life time.
     */
    const NODE_AVAILABILITY_CHECK_PERIOD = 86400;

    /**
     * Time in seconds, server node information life time.
     */
    const INACTIVE_NODE_STORAGE_PERIOD = 259200;

    /**
     * Servers data array.
     *
     * @var array
     */
    private $_aServersData = array();

    /**
     * Initiates servers array with content from configuration.
     */
    public function __construct()
    {
        $this->_aServersData = (array) \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('aServersData');
    }

    /**
     * Returns server based on server id.
     *
     * @param string $sServerId
     *
     * @return \OxidEsales\Eshop\Core\ApplicationServer
     */
    public function getServer($sServerId)
    {
        return $this->_createServer($sServerId, $this->_getServerData($sServerId));
    }

    /**
     * Saves given server information to config.
     *
     * @param \OxidEsales\Eshop\Core\ApplicationServer $oServer
     */
    public function saveServer($oServer)
    {
        $aServersData = $this->_getServersData();
        $aServersData[$oServer->getId()] = array(
            'id'                => $oServer->getId(),
            'timestamp'         => $oServer->getTimestamp(),
            'ip'                => $oServer->getIp(),
            'lastFrontendUsage' => $oServer->getLastFrontendUsage(),
            'lastAdminUsage'    => $oServer->getLastAdminUsage(),
            'isValid'           => $oServer->isValid()
        );

        $this->_save($aServersData);
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
     * @param string $sServerId
     *
     * @return array
     */
    protected function _getServerData($sServerId)
    {
        $aServers = $this->_getServersData();

        return array_key_exists($sServerId, $aServers) ? $aServers[$sServerId] : array();
    }

    /**
     * Creates oxApplicationServer from given server id and data.
     *
     * @param string $sServerId
     * @param array  $aData
     *
     * @return \OxidEsales\Eshop\Core\ApplicationServer
     */
    protected function _createServer($sServerId, $aData = array())
    {
        /** @var \OxidEsales\Eshop\Core\ApplicationServer $oAppServer */
        $oAppServer = oxNew(\OxidEsales\Eshop\Core\ApplicationServer::class);

        $oAppServer->setId($sServerId);
        $oAppServer->setTimestamp($this->_getServerParameter($aData, 'timestamp'));
        $oAppServer->setIp($this->_getServerParameter($aData, 'serverIp'));
        $oAppServer->setLastFrontendUsage($this->_getServerParameter($aData, 'lastFrontendUsage'));
        $oAppServer->setLastAdminUsage($this->_getServerParameter($aData, 'lastAdminUsage'));
        $oAppServer->setIsValid($this->_getServerParameter($aData, 'isValid'));

        return $oAppServer;
    }

    /**
     * Gets server parameter.
     *
     * @param array  $aData Data
     * @param string $sName Name
     *
     * @return mixed
     */
    protected function _getServerParameter($aData, $sName)
    {
        return array_key_exists($sName, $aData) ? $aData[$sName] : null;
    }

    /**
     * Return active server nodes
     *
     * @return array
     */
    public function getServers()
    {
        $this->markInActiveServers();
        $this->deleteInActiveServers();

        $aServers = $this->_getServersData();
        $aValidServers = array();

        foreach ($aServers as $aServer) {
            if ($aServer['isValid']) {
                unset($aServer['isValid']);
                unset($aServer['timestamp']);
                $aValidServers[] = $aServer;
            }
        }

        return $aValidServers;
    }

    /**
     * Removes server node information
     *
     * @param string $sServerId Server id
     */
    public function deleteServer($sServerId)
    {
        $aServersData = $this->_getServersData();
        unset($aServersData[$sServerId]);
        $this->_save($aServersData);
    }

    /**
     * Mark servers as inactive if they are not used anymore
     */
    public function markInActiveServers()
    {
        $aServersData = $this->_getServersData();

        foreach ($aServersData as $sServerId => $aServerData) {
            if ($aServerData['timestamp'] < \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - self::NODE_AVAILABILITY_CHECK_PERIOD) {
                $oServer = $this->getServer($sServerId);
                $oServer->setIsValid(false);
                $this->saveServer($oServer);
            }
        }
    }

    /**
     * Removes information about old and not used servers
     */
    public function deleteInActiveServers()
    {
        $aServersData = $this->_getServersData();

        foreach ($aServersData as $sServerId => $aServerData) {
            if ($aServerData['timestamp'] < \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - self::INACTIVE_NODE_STORAGE_PERIOD) {
                $this->deleteServer($sServerId);
            }
        }
    }

    /**
     * Saves servers data.
     *
     * @param array $aServersData Servers data
     */
    protected function _save($aServersData)
    {
        \OxidEsales\Eshop\Core\Registry::getConfig()->saveSystemConfigParameter('arr', 'aServersData', $aServersData);
        $this->_aServersData = $aServersData;
    }
}
