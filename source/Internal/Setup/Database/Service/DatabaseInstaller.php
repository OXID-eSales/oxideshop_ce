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
    /**
     * @var DatabaseCreatorInterface
     */
    private $creator;

    /**
     * @var DatabaseInitiatorInterface
     */
    private $initiator;

    /**
     * @var ConfigFileDaoInterface
     */
    private $configFileDao;

    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    /**
     * DatabaseInstaller constructor.
     * @param DatabaseCreatorInterface $creator
     * @param DatabaseInitiatorInterface $initiator
     * @param ConfigFileDaoInterface $configFileDao
     * @param BasicContextInterface $basicContext
     */
    public function __construct(
        DatabaseCreatorInterface $creator,
        DatabaseInitiatorInterface $initiator,
        ConfigFileDaoInterface $configFileDao,
        BasicContextInterface $basicContext
    ) {
        $this->creator = $creator;
        $this->initiator = $initiator;
        $this->configFileDao = $configFileDao;
        $this->basicContext = $basicContext;
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
        } catch (DatabaseExistsException $exception) {
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
        if (\opcache_get_status() !== false) {
            \opcache_invalidate($this->basicContext->getConfigFilePath());
        }
    }
}
