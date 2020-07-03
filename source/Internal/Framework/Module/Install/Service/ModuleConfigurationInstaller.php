<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Exception\InvalidMetaDataException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Webmozart\PathUtil\Path;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service\{
    ModuleConfigurationMergingServiceInterface
};

class ModuleConfigurationInstaller implements ModuleConfigurationInstallerInterface
{
    /**
     * @var ProjectConfigurationDaoInterface
     */
    private $projectConfigurationDao;

    /**
     * @var BasicContextInterface
     */
    private $context;

    /**
     * @var ModuleConfigurationMergingServiceInterface
     */
    private $moduleConfigurationMergingService;

    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $metadataModuleConfigurationDao;

    /**
     * @param ProjectConfigurationDaoInterface $projectConfigurationDao
     * @param BasicContextInterface $context
     * @param ModuleConfigurationMergingServiceInterface $moduleConfigurationMergingService
     * @param ModuleConfigurationDaoInterface $metadataModuleConfigurationDao
     */
    public function __construct(
        ProjectConfigurationDaoInterface $projectConfigurationDao,
        BasicContextInterface $context,
        ModuleConfigurationMergingServiceInterface $moduleConfigurationMergingService,
        ModuleConfigurationDaoInterface $metadataModuleConfigurationDao
    ) {
        $this->projectConfigurationDao = $projectConfigurationDao;
        $this->context = $context;
        $this->moduleConfigurationMergingService = $moduleConfigurationMergingService;
        $this->metadataModuleConfigurationDao = $metadataModuleConfigurationDao;
    }

    /**
     * @param string $moduleSourcePath
     * @param string $moduleTargetPath
     */
    public function install(string $moduleSourcePath, string $moduleTargetPath): void
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);

        $moduleConfiguration->setPath($this->getModuleRelativePath($moduleTargetPath));

        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();
        $projectConfiguration = $this->addModuleConfigurationToAllShops($moduleConfiguration, $projectConfiguration);

        $this->projectConfigurationDao->save($projectConfiguration);
    }

    /**
     * @param string $modulePath
     */
    public function uninstall(string $modulePath): void
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($modulePath);
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
     * @param string $moduleFullPath
     *
     * @return bool
     * @throws InvalidMetaDataException
     */
    public function isInstalled(string $moduleFullPath): bool
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleFullPath);
        $projectConfiguration = $this->projectConfigurationDao->getConfiguration();

        foreach ($projectConfiguration->getShopConfigurations() as $shopConfiguration) {
            /** @var $shopConfiguration ShopConfiguration */
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
     * @param string $moduleTargetPath
     * @return string
     */
    private function getModuleRelativePath(string $moduleTargetPath): string
    {
        return Path::isRelative($moduleTargetPath)
            ? $moduleTargetPath
            : Path::makeRelative($moduleTargetPath, $this->context->getModulesPath());
    }
}
