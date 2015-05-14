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

/**
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class oxServerProcessor extends oxSuperCfg
{

    /** @var oxServersManager */
    private $_oServerNodesManager;

    /** @var oxServerChecker */
    private $_oServerNodeChecker;

    /** @var oxUtilsServer */
    private $_oUtilsServer;

    /** @var oxUtilsDate */
    private $_oUtilsDate;

    /**
     * Gets server node checker.
     *
     * @return oxServerChecker
     */
    protected function _getServerNodeChecker()
    {
        return $this->_oServerNodeChecker;
    }

    /**
     * Gets server node manager.
     *
     * @return oxServersManager
     */
    protected function _getServerNodesManager()
    {
        return $this->_oServerNodesManager;
    }

    /**
     * Gets utils server.
     *
     * @return oxUtilsServer
     */
    protected function _getUtilsServer()
    {
        return $this->_oUtilsServer;
    }

    /**
     * Gets utils date.
     *
     * @return oxUtilsDate
     */
    protected function _getUtilsDate()
    {
        return $this->_oUtilsDate;
    }

    /**
     * Sets dependencies.
     *
     * @param oxServersManager $oServerNodesManager
     * @param oxServerChecker  $oServerNodeChecker
     * @param oxUtilsServer    $oUtilsServer
     * @param oxUtilsDate      $oUtilsDate
     */
    public function __construct(
        oxServersManager $oServerNodesManager,
        oxServerChecker $oServerNodeChecker,
        oxUtilsServer $oUtilsServer,
        oxUtilsDate $oUtilsDate
    ) {
        $this->_oServerNodesManager = $oServerNodesManager;
        $this->_oServerNodeChecker = $oServerNodeChecker;
        $this->_oUtilsServer = $oUtilsServer;
        $this->_oUtilsDate = $oUtilsDate;
    }

    /**
     * Renew frontend server node information if it is outdated or it does not exist.
     */
    public function process()
    {
        $oNodesManager = $this->_getServerNodesManager();
        $sServerNodeId = $this->_getUtilsServer()->getServerNodeId();
        $oNode = $oNodesManager->getServer($sServerNodeId);

        $oNodeChecker = $this->_getServerNodeChecker();
        if (!$oNodeChecker->check($oNode)) {
            $this->_updateNodeInformation($oNode);
            $oNodesManager->saveServer($oNode);
        }
    }

    /**
     * Updates mode information.
     *
     * @param oxApplicationServer $oNode
     */
    private function _updateNodeInformation($oNode)
    {
        $oUtilsServer = $this->_getUtilsServer();
        $sServerNodeId = $oUtilsServer->getServerNodeId();
        $oUtilsDate = $this->_getUtilsDate();

        $oNode->setId($sServerNodeId);
        $oNode->setIp($oUtilsServer->getServerIp());
        $oNode->setTimestamp($oUtilsDate->getTime());
        $oNode->setIsValid();
        if ($this->isAdmin()) {
            $oNode->setLastAdminUsage($oUtilsDate->getTime());
        } else {
            $oNode->setLastFrontendUsage($oUtilsDate->getTime());
        }
    }
}
