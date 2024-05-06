<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ActiveClassExtensionChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ActiveModulesDataProvider implements ActiveModulesDataProviderInterface
{
    public function __construct(
        private readonly ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private readonly ModulePathResolverInterface $modulePathResolver,
        private readonly ContextInterface $context,
        private readonly ModuleCacheInterface $moduleCache,
        private readonly ActiveClassExtensionChainResolverInterface $activeClassExtensionChainResolver
    ) {
    }

    /** @inheritDoc */
    public function getModuleIds(): array
    {
        $moduleIds = [];

        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            $moduleIds[] = $moduleConfiguration->getId();
        }

        return $moduleIds;
    }

    /** @inheritDoc */
    public function getModulePaths(): array
    {
        $cacheKey = 'absolute_module_paths';

        if (!$this->moduleCache->exists($cacheKey)) {
            $this->moduleCache->put(
                $cacheKey,
                $this->collectModulePathsForCaching()
            );
        }
        return $this->moduleCache->get($cacheKey);
    }

    /** @inheritDoc */
    public function getControllers(): array
    {
        $cacheKey = 'controllers';

        if (!$this->moduleCache->exists($cacheKey)) {
            $this->moduleCache->put($cacheKey, $this->collectControllersForCaching());
        }

        return $this->createControllersFromData(
            $this->moduleCache->get($cacheKey)
        );
    }

    /** @inheritDoc */
    public function getClassExtensions(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'module_class_extensions';

        if (!$this->moduleCache->exists($cacheKey)) {
            $this->moduleCache->put(
                $cacheKey,
                $this->activeClassExtensionChainResolver->getActiveExtensionChain($shopId)->getChain()
            );
        }

        return $this->moduleCache->get($cacheKey);
    }

    /** @return array */
    private function collectModulePathsForCaching(): array
    {
        $modulePaths = [];
        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            $modulePaths[$moduleConfiguration->getId()] = $this
                ->modulePathResolver
                ->getFullModulePathFromConfiguration(
                    $moduleConfiguration->getId(),
                    $this->context->getCurrentShopId()
                );
        }
        return $modulePaths;
    }

    /** @return array */
    private function collectControllersForCaching(): array
    {
        $controllers = [];
        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            foreach ($moduleConfiguration->getControllers() as $controller) {
                $controllers[$controller->getId()] = $controller->getControllerClassNameSpace();
            }
        }
        return $controllers;
    }

    /** @return ModuleConfiguration[] */
    private function getActiveModuleConfigurations(): array
    {
        $moduleConfigurations = [];
        $shopId = $this->context->getCurrentShopId();

        foreach ($this->moduleConfigurationDao->getAll($shopId) as $moduleConfiguration) {
            if ($moduleConfiguration->isActivated()) {
                $moduleConfigurations[] = $moduleConfiguration;
            }
        }
        return $moduleConfigurations;
    }

    private function createControllersFromData(array $data): array
    {
        $controllers = [];
        foreach ($data as $id => $namespace) {
            $controllers[] = new ModuleConfiguration\Controller($id, $namespace);
        }

        return $controllers;
    }
}
