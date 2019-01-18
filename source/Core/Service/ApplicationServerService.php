<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Service;

/**
 * Manages application server information.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ApplicationServerService implements \OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface
{
    /**
     * The Dao object for application server.
     *
     * @var \OxidEsales\Eshop\Core\Dao\ApplicationServerDaoInterface
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
     * @param \OxidEsales\Eshop\Core\Dao\ApplicationServerDaoInterface $appServerDao The Dao of application server.
     * @param \OxidEsales\Eshop\Core\UtilsServer                       $utilsServer
     * @param int                                                      $currentTime  The current time - timestamp.
     */
    public function __construct(
        \OxidEsales\Eshop\Core\Dao\ApplicationServerDaoInterface $appServerDao,
        $utilsServer,
        $currentTime
    ) {
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
     * @throws \OxidEsales\Eshop\Core\Exception\NoResultException
     *
     * @return \OxidEsales\Eshop\Core\DataObject\ApplicationServer
     */
    public function loadAppServer($id)
    {
        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer */
        $appServer = $this->appServerDao->findAppServer($id);
        if ($appServer === null) {
            /** @var \OxidEsales\Eshop\Core\Exception\NoResultException $exception */
            $exception = oxNew(\OxidEsales\Eshop\Core\Exception\NoResultException::class);
            throw $exception;
        }
        return $appServer;
    }

    /**
     * Removes server node information.
     *
     * @param string $serverId The Id of the application server to delete.
     */
    public function deleteAppServerById($serverId)
    {
        $this->appServerDao->delete($serverId);
    }

    /**
     * Saves application server data.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer
     */
    public function saveAppServer($appServer)
    {
        $this->appServerDao->save($appServer);
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
                $activeServerList[$server->getId()] = $server;
            }
        }
        return $activeServerList;
    }

    /**
     * Deletes all application servers, that are longer not active.
     */
    private function cleanupAppServers()
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
     * Renews application server information when it is call in admin area and
     * if it is outdated or if it does not exist.
     */
    public function updateAppServerInformationInAdmin()
    {
        $this->updateAppServerInformation(true);
    }

    /**
     * Renews application server information when it is call in frontend and
     * if it is outdated or if it does not exist.
     */
    public function updateAppServerInformationInFrontend()
    {
        $this->updateAppServerInformation(false);
    }

    /**
     * Renews application server information if it is outdated or if it does not exist.
     *
     * @throws \Exception
     *
     * @param bool $adminMode The status of admin mode
     */
    public function updateAppServerInformation($adminMode)
    {
        $this->appServerDao->startTransaction();
        try {
            /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer */
            $appServer = $this->appServerDao->findAppServer($this->utilsServer->getServerNodeId());
            if ($appServer === null) {
                $this->addNewAppServerData($adminMode);
            } elseif ($appServer->needToUpdate($this->currentTime)) {
                $this->updateAppServerData($appServer, $adminMode);
            }
        } catch (\Exception $exception) {
            $this->appServerDao->rollbackTransaction();
            throw $exception;
        }
        $this->appServerDao->commitTransaction();
    }

    /**
     * Updates application server with the newest information.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer The application server to update.
     * @param bool                                                $adminMode The status of admin mode.
     */
    private function updateAppServerData($appServer, $adminMode)
    {
        $appServer->setId($this->utilsServer->getServerNodeId());
        $appServer->setIp($this->utilsServer->getServerIp());
        $appServer->setTimestamp($this->currentTime);
        if ($adminMode) {
            $appServer->setLastAdminUsage($this->currentTime);
        } else {
            $appServer->setLastFrontendUsage($this->currentTime);
        }
        $this->saveAppServer($appServer);
        $this->cleanupAppServers();
    }

    /**
     * Adds new application server.
     *
     * @param bool $adminMode The status of admin mode.
     */
    private function addNewAppServerData($adminMode)
    {
        /** @var \OxidEsales\Eshop\Core\DataObject\ApplicationServer $appServer */
        $appServer = oxNew(\OxidEsales\Eshop\Core\DataObject\ApplicationServer::class);

        $appServer->setId($this->utilsServer->getServerNodeId());
        $appServer->setIp($this->utilsServer->getServerIp());
        $appServer->setTimestamp($this->currentTime);
        if ($adminMode) {
            $appServer->setLastAdminUsage($this->currentTime);
        } else {
            $appServer->setLastFrontendUsage($this->currentTime);
        }
        $this->saveAppServer($appServer);
        $this->cleanupAppServers();
    }
}
