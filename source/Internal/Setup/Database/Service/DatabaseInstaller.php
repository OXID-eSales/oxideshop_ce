<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\DatabaseViewsGenerator\ViewsGenerator;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class DatabaseInstaller implements DatabaseInstallerInterface
{
    public function __construct(
        private DatabaseCreatorInterface $creator,
        private DatabaseInitiatorInterface $initiator,
        private BasicContextInterface $basicContext
    ) {
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $username
     * @param string $password
     * @param string $name
     */
    public function install(string $host, int $port, string $username, string $password, string $name): void
    {
        try {
            $this->creator->createDatabase($host, $port, $username, $password, $name);
        } catch (DatabaseExistsException) {
        }

        $this->resetConfigFileOpcache();

        $this->initiator->initiateDatabase($host, $port, $username, $password, $name);

        $this->generateViews();
    }

    private function generateViews(): void
    {
        (new ViewsGenerator())->generate();
    }

    private function resetConfigFileOpcache(): void
    {
        if (function_exists('opcache_get_status') && opcache_get_status() !== false) {
            opcache_invalidate($this->basicContext->getConfigFilePath());
        }
    }
}
