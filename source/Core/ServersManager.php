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
     * @var \OxidEsales\Eshop\Core\Service\ApplicationServerService
     */
    private $appServerService;

    /**
     * @var \OxidEsales\Eshop\Core\Service\ApplicationServerExporter
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
        /** @var \OxidEsales\Eshop\Core\UtilsServer $utilsServer */
        $utilsServer = oxNew(\OxidEsales\Eshop\Core\UtilsServer::class);
        $this->appServerService = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerService::class, $appServerDao, $utilsServer, \OxidEsales\Eshop\Core\Registry::get("oxUtilsDate")->getTime());
    }

    /**
     * Returns server based on server id.
     *
     * @param string $serverId
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer
     */
    public function getServer($serverId)
    {
        return $this->appServerService->loadAppServer($serverId);
    }

    /**
     * Saves given server information to config.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $oServer
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
        $appServerFacade = oxNew(\OxidEsales\Eshop\Core\Service\ApplicationServerExporter::class, $this->appServerService);
        return $appServerFacade->export();
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
     * Returns all servers information array from configuration.
     *
     * @return array
     */
    public function getServersData()
    {
        return $this->appServerService->loadAppServerList();
    }

}
