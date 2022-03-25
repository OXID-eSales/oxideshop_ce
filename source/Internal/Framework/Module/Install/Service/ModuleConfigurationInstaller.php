<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Webmozart\PathUtil\Path;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\{
    ModuleConfigurationMergingServiceInterface
};

class ModuleConfigurationInstaller implements ModuleConfigurationInstallerInterface
{
    /**
     * @param ModuleConfigurationMergingServiceInterface $moduleConfigurationMergingService
     */
    public function __construct(private ProjectConfigurationDaoInterface $projectConfigurationDao, private BasicContextInterface $context, private ModuleConfigurationMergingServiceInterface $moduleConfigurationMergingService, private ModuleConfigurationDaoInterface $metadataModuleConfigurationDao)
    {
    }

    /**
     * @param string $moduleSourcePath
     */
    public function install(string $moduleSourcePath): void
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);

        $moduleConfiguration->setModuleSource($this->getModuleSourceRelativePath($moduleSourcePath));

        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $projectConfiguration = $this->addModuleConfigurationToAllShops($moduleConfiguration, $projectConfiguration);

        $this->projectConfigurationDao->save($projectConfiguration);
    }

    /**
     * @param string $moduleSourcePath
     *
     * @throws ModuleConfigurationNotFoundException
     */
    public function uninstall(string $moduleSourcePath): void
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();

        foreach ($projectConfiguration->getShopConfigurations() as $shopConfiguration) {
            if ($shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
                $shopConfiguration->deleteModuleConfiguration($moduleConfiguration->getId());
            }
        }

        $this->projectConfigurationDao->save($projectConfiguration);
    }

    /**
     * @param string $moduleId
     *
     * @throws ModuleConfigurationNotFoundException
     */
    public function uninstallById(string $moduleId): void
    {
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();

        foreach ($projectConfiguration->getShopConfigurations() as $shopConfiguration) {
            if ($shopConfiguration->getModuleConfiguration($moduleId)) {
                $shopConfiguration->deleteModuleConfiguration($moduleId);
            }
        }

        $this->projectConfigurationDao->save($projectConfiguration);
    }

    /**
     * @param string $moduleSourcePath
     *
     * @return bool
     */
    public function isInstalled(string $moduleSourcePath): bool
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();

        foreach ($projectConfiguration->getShopConfigurations() as $shopConfiguration) {
            if ($shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param ModuleConfiguration  $moduleConfiguration
     * @param ProjectConfiguration $projectConfiguration
     *
     * @return ProjectConfiguration
     */
    private function addModuleConfigurationToAllShops(
        ModuleConfiguration $moduleConfiguration,
        ProjectConfiguration $projectConfiguration
    ): ProjectConfiguration {

        foreach ($projectConfiguration->getShopConfigurations() as $shopConfiguration) {
            $this->moduleConfigurationMergingService->merge($shopConfiguration, $moduleConfiguration);
        }

        return $projectConfiguration;
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
