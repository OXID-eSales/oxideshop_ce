<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ModuleMigrationConfigLocator implements ModuleMigrationConfigLocatorInterface
{
    private const MIGRATION_CONFIG_FILE = 'migration/migrations.yml';

    public function __construct(
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private BasicContextInterface $context,
    ) {
    }

    public function getMigrationConfigPaths(): array
    {
        $configPaths = [];
        $moduleConfigurations = $this->moduleConfigurationDao->getAll($this->context->getDefaultShopId());

        foreach ($moduleConfigurations as $moduleConfiguration) {
            $configPath = $this->getMigrationConfigPath($moduleConfiguration);

            if (is_file($configPath)) {
                $configPaths[$moduleConfiguration->getId()] = $configPath;
            }
        }

        return $configPaths;
    }

    private function getMigrationConfigPath(ModuleConfiguration $moduleConfiguration): string
    {
        return Path::join(
            $this->context->getShopRootPath(),
            $moduleConfiguration->getModuleSource(),
            self::MIGRATION_CONFIG_FILE
        );
    }
}
