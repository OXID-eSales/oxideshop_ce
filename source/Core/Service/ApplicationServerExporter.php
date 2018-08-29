<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Service;

/**
 * Prepare application servers information for export.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ApplicationServerExporter implements \OxidEsales\Eshop\Core\Service\ApplicationServerExporterInterface
{
    /**
     * The service class of application server.
     *
     * @var \OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface
     */
    private $appServerService;

    /**
     * ApplicationServerExporter constructor.
     *
     * @param \OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface $appServerService
     */
    public function __construct(\OxidEsales\Eshop\Core\Service\ApplicationServerServiceInterface $appServerService)
    {
        $this->appServerService = $appServerService;
    }

    /**
     * Return an array of active application servers.
     *
     * @return array
     */
    public function exportAppServerList()
    {
        $activeServerCollection = [];

        $activeServers = $this->appServerService->loadActiveAppServerList();
        if (is_array($activeServers) && !empty($activeServers)) {
            foreach ($activeServers as $server) {
                $activeServerCollection[] = $this->convertToArray($server);
            }
        }

        return $activeServerCollection;
    }

    /**
     * Converts ApplicationServer object into array for export.
     *
     * @param \OxidEsales\Eshop\Core\DataObject\ApplicationServer $server
     *
     * @return array
     */
    private function convertToArray($server)
    {
        $activeServer = [
            'id' => $server->getId(),
            'ip' => $server->getIp(),
            'lastFrontendUsage' => $server->getLastFrontendUsage(),
            'lastAdminUsage' => $server->getLastAdminUsage()
        ];
        return $activeServer;
    }
}
