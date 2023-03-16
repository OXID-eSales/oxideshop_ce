<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\{
    ModuleConfigurationMergingServiceInterface
};

class ModuleConfigurationInstaller implements ModuleConfigurationInstallerInterface
{
    public function __construct(
        private ShopConfigurationDaoInterface $shopConfigurationDao,
        private BasicContextInterface $context,
        private ModuleConfigurationMergingServiceInterface $moduleConfigurationMergingService,
        private ModuleConfigurationDaoInterface $metadataModuleConfigurationDao
    ) {
    }

    /**
     * @param string $moduleSourcePath
     */
    public function install(string $moduleSourcePath): void
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);
        $moduleConfiguration->setModuleSource($this->getModuleSourceRelativePath($moduleSourcePath));

        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            $this->moduleConfigurationMergingService->merge($shopConfiguration, $moduleConfiguration);
            $this->shopConfigurationDao->save($shopConfiguration, $shopId);
        }
    }

    /**
     * @param string $moduleSourcePath
     *
     * @throws ModuleConfigurationNotFoundException
     */
    public function uninstall(string $moduleSourcePath): void
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);

        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            if ($shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
                $shopConfiguration->deleteModuleConfiguration($moduleConfiguration->getId());
            }
            $this->shopConfigurationDao->save($shopConfiguration, $shopId);
        }
    }

    /**
     * @param string $moduleId
     *
     * @throws ModuleConfigurationNotFoundException
     */
    public function uninstallById(string $moduleId): void
    {
        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            if ($shopConfiguration->hasModuleConfiguration($moduleId)) {
                $shopConfiguration->deleteModuleConfiguration($moduleId);
            }
            $this->shopConfigurationDao->save($shopConfiguration, $shopId);
        }
    }

    /**
     * @param string $moduleSourcePath
     *
     * @return bool
     */
    public function isInstalled(string $moduleSourcePath): bool
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);

        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            if ($shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $moduleSourcePath
     *
     * @return string
     */
    private function getModuleSourceRelativePath(string $moduleSourcePath): string
    {
        return Path::makeRelative($moduleSourcePath, $this->context->getShopRootPath());
    }
}
