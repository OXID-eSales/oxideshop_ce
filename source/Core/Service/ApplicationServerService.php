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

namespace OxidEsales\EshopCommunity\Core\Service;

/**
 * Manages application server information.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ApplicationServerService implements \OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface
{
    /**
     * The Dao object for application server.
     *
     * @var \OxidEsales\Eshop\Core\Dao\BaseDaoInterface
     */
    private $appServerDao;

    /**
     * Current checking time - timestamp.
     *
     * @var int
     */
    private $currentTime = 0;

    /**
     * Server data manipulation class
     *
     * @var \OxidEsales\Eshop\Core\UtilsServer
     */
    private $utilsServer;

    /**
     * ApplicationServerService constructor.
     *
     * @param \OxidEsales\Eshop\Core\Dao\BaseDaoInterface $appServerDao The Dao object of application server.
     * @param \OxidEsales\Eshop\Core\UtilsServer          $utilsServer
     * @param int                                         $currentTime  The current time - timestamp.
     */
    public function __construct(\OxidEsales\Eshop\Core\Dao\BaseDaoInterface $appServerDao, $utilsServer, $currentTime)
    {
        $this->appServerDao = $appServerDao;
        $this->utilsServer = $utilsServer;
        $this->currentTime = $currentTime;
    }

    /**
     * Returns an array of all application servers.
     *
     * @return array
     */
    public function loadAppServerList()
    {
        return $this->appServerDao->findAll();
    }

    /**
     * Load the application server for given id.
     *
     * @param string $id The id of the application server to load.
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer
     */
    public function loadAppServer($id)
    {
        return $this->appServerDao->findById($id);
    }

    /**
     * Removes server node information.
     *
     * @param string $serverId The Id of the application server to delete.
     *
     * @return bool
     */
    public function deleteAppServerById($serverId)
    {
        return $this->appServerDao->delete($serverId);
    }

    /**
     * Saves application server data.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer
     *
     * @return int
     */
    public function saveAppServer($appServer)
    {
        if ($this->appServerDao->findById($appServer->getId()) !== false) {
            $effectedRows = $this->appServerDao->update($appServer);
        } else {
            $effectedRows = $this->appServerDao->insert($appServer);
        }
        return $effectedRows;
    }

    /**
     * Returns an array of all only active application servers.
     *
     * @return array
     */
    public function loadActiveAppServerList()
    {
        $allFoundServers = $this->loadAppServerList();
        return $this->filterActiveAppServers($allFoundServers);
    }

    /**
     * Filter only active application servers from given list.
     *
     * @param array $appServerList The list of application servers.
     *
     * @return array
     */
    protected function filterActiveAppServers($appServerList)
    {
        $activeServerList = [];
        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer $server */
        foreach ($appServerList as $server) {
            if ($server->isInUse($this->currentTime)) {
                $activeServerList[] = $server;
            }
        }
        return $activeServerList;
    }

    /**
     * Deletes all application servers, that are longer not active.
     */
    public function cleanupAppServers()
    {
        $allFoundServers = $this->loadAppServerList();
        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer $server */
        foreach ($allFoundServers as $server) {
            if ($server->needToDelete($this->currentTime)) {
                $this->deleteAppServerById($server->getId());
            }
        }
    }

    /**
     * Renews application server information if it is outdated or if it does not exist.
     *
     * @param bool $adminMode The status of admin mode
     */
    public function updateAppServerInformation($adminMode = false)
    {
        $appServer = $this->loadAppServer($this->utilsServer->getServerNodeId());

        if ($appServer->needToUpdate($this->currentTime)) {
            $this->updateAppServerData($appServer, $adminMode);
            $this->saveAppServer($appServer);
        }
    }

    /**
     * Updates application server with the newest information.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer The application server to update.
     * @param bool                                                $adminMode The status of admin mode.
     */
    private function updateAppServerData($appServer, $adminMode)
    {
        $appServer->setIp($this->utilsServer->getServerIp());
        $appServer->setTimestamp($this->currentTime);
        if ($adminMode) {
            $appServer->setLastAdminUsage($this->currentTime);
        } else {
            $appServer->setLastFrontendUsage($this->currentTime);
        }
    }
}
