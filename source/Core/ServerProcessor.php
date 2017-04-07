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

use oxServerChecker;
use oxUtilsServer;
use oxUtilsDate;

/**
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ServerProcessor extends \OxidEsales\Eshop\Core\Base
{

    /** @var \OxidEsales\Eshop\Core\ServersManager */
    private $_oServerNodesManager;

    /** @var \OxidEsales\Eshop\Core\ServerChecker */
    private $_oServerNodeChecker;

    /** @var \OxidEsales\Eshop\Core\UtilsServer */
    private $_oUtilsServer;

    /** @var \OxidEsales\Eshop\Core\UtilsDate */
    private $_oUtilsDate;

    /**
     * Gets server node checker.
     *
     * @return \OxidEsales\Eshop\Core\ServerChecker
     */
    protected function _getServerNodeChecker()
    {
        return $this->_oServerNodeChecker;
    }

    /**
     * Gets server node manager.
     *
     * @return \OxidEsales\Eshop\Core\ServersManager
     */
    protected function _getServerNodesManager()
    {
        return $this->_oServerNodesManager;
    }

    /**
     * Gets utils server.
     *
     * @return \OxidEsales\Eshop\Core\UtilsServer
     */
    protected function _getUtilsServer()
    {
        return $this->_oUtilsServer;
    }

    /**
     * Gets utils date.
     *
     * @return \OxidEsales\Eshop\Core\UtilsDate
     */
    protected function _getUtilsDate()
    {
        return $this->_oUtilsDate;
    }

    /**
     * Sets dependencies.
     *
     * @param \OxidEsales\Eshop\Core\ServersManager $oServerNodesManager
     * @param \OxidEsales\Eshop\Core\ServerChecker  $oServerNodeChecker
     * @param \OxidEsales\Eshop\Core\UtilsServer    $oUtilsServer
     * @param \OxidEsales\Eshop\Core\UtilsDate      $oUtilsDate
     */
    public function __construct(
        \OxidEsales\Eshop\Core\ServersManager $oServerNodesManager,
        \OxidEsales\Eshop\Core\ServerChecker $oServerNodeChecker,
        \OxidEsales\Eshop\Core\UtilsServer $oUtilsServer,
        \OxidEsales\Eshop\Core\UtilsDate $oUtilsDate
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
     * @param \OxidEsales\Eshop\Core\ApplicationServer $oNode
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
