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
 * oxServerNodeProcessor
 *
 * @internal Do not make a module extension for this class.
 * @see http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxServerNodeProcessor extends oxSuperCfg
{
    /** @var oxServerNodesManager */
    private $_oServerNodesManager;

    /** @var oxServerChecker */
    private $_oServerNodeChecker;

    /** @var oxUtilsServer  */
    private $_oUtilsServer;

    /** @var oxUtilsDate  */
    private $_oUtilsDate;

    /**
     * @return oxServerChecker
     */
    protected function _getServerNodeChecker()
    {
        return $this->_oServerNodeChecker;
    }

    /**
     * @return oxServerNodesManager
     */
    protected function _getServerNodesManager()
    {
        return $this->_oServerNodesManager;
    }

    /**
     * @return oxUtilsServer
     */
    protected function _getUtilsServer()
    {
        return $this->_oUtilsServer;
    }

    /**
     * @return oxUtilsDate
     */
    protected function _getUtilsDate()
    {
        return $this->_oUtilsDate;
    }

    /**
     * @param oxServerNodesManager $oServerNodesManager
     * @param oxServerChecker $oServerNodeChecker
     * @param oxUtilsServer $oUtilsServer
     * @param oxUtilsDate $oUtilsDate
     */
    public function __construct(oxServerNodesManager $oServerNodesManager = null,
                                oxServerChecker $oServerNodeChecker = null,
                                oxUtilsServer $oUtilsServer = null,
                                oxUtilsDate $oUtilsDate = null)
    {
        if (is_null($oServerNodesManager)) {
            $oServerNodesManager = oxNew('oxServerNodesManager');
        }
        $this->_oServerNodesManager = $oServerNodesManager;

        if (is_null($oServerNodeChecker)) {
            $oServerNodeChecker = oxNew('oxServerNodeChecker');
        }
        $this->_oServerNodeChecker = $oServerNodeChecker;

        if (is_null($oUtilsServer)) {
            $oUtilsServer = oxNew('oxUtilsServer');
        }
        $this->_oUtilsServer = $oUtilsServer;

        if (is_null($oUtilsDate)) {
            $oUtilsDate = oxRegistry::get('oxUtilsDate');
        }
        $this->_oUtilsDate = $oUtilsDate;
    }

    /**
     * Renew frontend server node information if it is outdated or it does not exist.
     */
    public function process()
    {
        $oNodesManager = $this->_getServerNodesManager();
        $sServerNodeId = $this->_getUtilsServer()->getServerNodeId();
        $oNode = $oNodesManager->getNode($sServerNodeId);

        $oNodeChecker = $this->_getServerNodeChecker();
        if (!$oNodeChecker->check($oNode)) {
            $this->_updateNodeInformation($oNode);
            $oNodesManager->saveNode($oNode);
        }
    }

    /**
     * @param oxServerNode $oNode
     */
    private function _updateNodeInformation($oNode)
    {
        $oUtilsServer = $this->_getUtilsServer();
        $sServerNodeId = $oUtilsServer->getServerNodeId();
        $oUtilsDate = $this->_getUtilsDate();

        $oNode->setId($sServerNodeId);
        $oNode->setIp($oUtilsServer->getServerIp());
        $oNode->setTimestamp($oUtilsDate->getTime());
        if ($this->isAdmin()) {
            $oNode->setLastAdminUsage($oUtilsDate->getTime());
        } else {
            $oNode->setLastFrontendUsage($oUtilsDate->getTime());
        }
    }
}