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
use oxDb;
use oxUtilsObject;

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
     * @var \OxidEsales\Eshop\Core\Service\ApplicationServerService
     */
    private $appServerService;

    /**
     * @var \OxidEsales\Eshop\Core\Service\ApplicationServerFacade
     */
    private $appServerFacade;

    /**
     * ServersManager constructor.
     */
    public function __construct()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $databaseProvider = oxNew(\OxidEsales\Eshop\Core\DatabaseProvider::class);
        $appServerDao = oxNew(\OxidEsales\Eshop\Core\Dao\ApplicationServerDao::class, $databaseProvider, $config);
        $this->appServerService = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class, $appServerDao);
    }

    /**
     * Returns server based on server id.
     *
     * @param string $serverId
     *
     * @return \OxidEsales\Eshop\Core\ApplicationServer
     */
    public function getServer($serverId)
    {
        return $this->appServerService->loadAppServer($serverId);
    }

    /**
     * Saves given server information to config.
     *
     * @param \OxidEsales\Eshop\Core\ApplicationServer $oServer
     */
    public function saveServer($oServer)
    {
        $this->appServerService->saveAppServer($oServer);
    }

    /**
     * Return active server nodes.
     *
     * @return array
     */
    public function getServers()
    {
        $appServerList = $this->getServersData();
        $appServerList = $this->markInActiveServers($appServerList);
        $appServerList = $this->deleteInActiveServers($appServerList);

        $activeServerList = array();
        /** @var \OxidEsales\Eshop\Core\ApplicationServer $server */
        foreach ($appServerList as $server) {
            if ($server->isValid()) {
                $activeServerList[] = $server;
            }
        }

        $this->appServerService->setActiveAppServerList($activeServerList);

        $appServerFacade = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerFacade::class, $this->appServerService);
        return $appServerFacade->getApplicationServerList();
    }

    /**
     * Removes server node information.
     *
     * @param string $sServerId Server id
     */
    public function deleteServer($sServerId)
    {
        $this->appServerService->deleteAppServerById($sServerId);
    }

    /**
     * Mark servers as inactive if they are not used anymore.
     *
     * @param array $appServerList Information of all servers data
     *
     * @return array $appServerList Information of all servers data
     */
    public function markInActiveServers($appServerList)
    {
        /** @var \OxidEsales\Eshop\Core\ApplicationServer $oServer */
        foreach ($appServerList as $oServer) {
            if ($this->needToCheckApplicationServerAvailability($oServer->getTimestamp())) {
                $oServer->setIsValid(false);
                $this->saveServer($oServer);
                $appServerList[$oServer->getId()] = $oServer;
            }
        }
        return $appServerList;
    }

    /**
     * Check if application server availability check period is over.
     *
     * @param int $timestamp A timestamp when last time server was checked.
     *
     * @return bool
     */
    protected function needToCheckApplicationServerAvailability($timestamp)
    {
        return (bool) ($timestamp < \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime() - self::NODE_AVAILABILITY_CHECK_PERIOD);
    }

    /**
     * Removes information about old and not used servers.
     *
     * @param array $appServerList Information of all servers data
     *
     * @return array $appServerList Information of all servers data
     */
    public function deleteInActiveServers($appServerList)
    {
        /** @var ApplicationServer $oServer */
        foreach ($appServerList as $oServer) {
            if ($this->needToDeleteInactiveApplicationServer($oServer->getTimestamp())) {
                $this->deleteServer($oServer->getId());
                unset($appServerList[$oServer->getId()]);
            }
        }
        return $appServerList;
    }

    /**
     * Check if application server availability check period is over.
     *
     * @param int $timestamp A timestamp when last time server was checked.
     *
     * @return bool
     */
    protected function needToDeleteInactiveApplicationServer($timestamp)
    {
        return (bool) ($timestamp < \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime() - self::INACTIVE_NODE_STORAGE_PERIOD);
    }

    /**
     * Returns all servers information array from configuration.
     *
     * @return array
     */
    public function getServersData()
    {
        return $this->appServerService->loadAppServerList();
    }

}
