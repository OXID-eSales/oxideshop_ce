<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\Chain\ClassExtensionsChainDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ExtensionNotInChainException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDaoInterface as MetadataDaoInterface;
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
        private MetadataDaoInterface $metadataModuleConfigurationDao,
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private ClassExtensionsChainDaoInterface $classExtensionsChainDao
    ) {
    }

    public function install(string $moduleSourcePath): void
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);
        $moduleConfiguration->setModuleSource($this->getModuleSourceRelativePath($moduleSourcePath));

        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            $mergedModuleConfiguration = $this
                ->moduleConfigurationMergingService
                ->merge($shopConfiguration, $moduleConfiguration)
                ->getModuleConfiguration($moduleConfiguration->getId());

            $this->moduleConfigurationDao->save($mergedModuleConfiguration, $shopId);
            $this->classExtensionsChainDao->saveChain($shopId, $shopConfiguration->getClassExtensionsChain());
        }
    }

    public function uninstall(string $moduleSourcePath): void
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);

        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            if ($shopConfiguration->hasModuleConfiguration($moduleConfiguration->getId())) {
                $this->removeModuleConfiguration($moduleConfiguration, $shopId);
            }
        }
    }

    public function uninstallById(string $moduleId): void
    {
        foreach ($this->shopConfigurationDao->getAll() as $shopId => $shopConfiguration) {
            if ($shopConfiguration->hasModuleConfiguration($moduleId)) {
                $this->removeModuleConfiguration($shopConfiguration->getModuleConfiguration($moduleId), $shopId);
            }
        }
    }

    public function isInstalled(string $moduleSourcePath): bool
    {
        $moduleConfiguration = $this->metadataModuleConfigurationDao->get($moduleSourcePath);

        return $this->shopConfigurationDao->get($this->context->getDefaultShopId())
            ->hasModuleConfiguration($moduleConfiguration->getId());
    }

    private function getModuleSourceRelativePath(string $moduleSourcePath): string
    {
        return Path::makeRelative($moduleSourcePath, $this->context->getShopRootPath());
    }

    private function removeModuleConfiguration(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        $this->moduleConfigurationDao->delete($moduleConfiguration->getId(), $shopId);

        $chain = $this->classExtensionsChainDao->getChain($shopId);
        foreach ($moduleConfiguration->getClassExtensions() as $classExtension) {
            try {
                $chain->removeExtension($classExtension);
            } catch (ExtensionNotInChainException) {
            }
        }
        $this->classExtensionsChainDao->saveChain($shopId, $chain);
    }
}
