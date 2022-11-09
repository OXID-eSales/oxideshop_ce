<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\Database\Service;

use OxidEsales\DatabaseViewsGenerator\ViewsGenerator;
use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Setup\ConfigFile\ConfigFileDaoInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class DatabaseInstaller implements DatabaseInstallerInterface
{
    public function __construct(
        private DatabaseCreatorInterface $creator,
        private DatabaseInitiatorInterface $initiator,
        private ConfigFileDaoInterface $configFileDao,
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

        $this->addCredentialsToConfigFile($host, (string) $port, $username, $password, $name);
        $this->resetConfigFileOpcache();
        $this->updateConfigFileInRegistry();

        $this->initiator->initiateDatabase($host, $port, $username, $password, $name);

        $this->generateViews();
    }

    private function addCredentialsToConfigFile(
        string $host,
        string $port,
        string $username,
        string $password,
        string $name
    ): void {
        $this->configFileDao->replacePlaceholder('dbHost', $host);
        $this->configFileDao->replacePlaceholder('dbPort', $port);
        $this->configFileDao->replacePlaceholder('dbUser', $username);
        $this->configFileDao->replacePlaceholder('dbPwd', $password);
        $this->configFileDao->replacePlaceholder('dbName', $name);
    }

    private function updateConfigFileInRegistry(): void
    {
        /**
         * @todo We should not use Registry or ConfigFile classes directly in internal namespace, but shop setup is
         *       very special case and we can't avoid it. It should be removed after config refactoring.
         */
        Registry::set(ConfigFile::class, new ConfigFile($this->basicContext->getConfigFilePath()));
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
