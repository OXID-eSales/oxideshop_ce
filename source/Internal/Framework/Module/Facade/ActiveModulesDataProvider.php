<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\CacheNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ActiveClassExtensionChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ActiveModulesDataProvider implements ActiveModulesDataProviderInterface
{
    public function __construct(
        private ModuleConfigurationDaoInterface $moduleConfigurationDao,
        private ModulePathResolverInterface $modulePathResolver,
        private ContextInterface $context,
        private ModuleCacheServiceInterface $moduleCacheService,
        private ActiveClassExtensionChainResolverInterface $activeClassExtensionChainResolver
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
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'absolute_module_paths';

        try {
            return $this->moduleCacheService->get($cacheKey, $shopId);
        } catch (CacheNotFoundException | \JsonException) {
            $data = $this->collectModulePaths();
            $this->moduleCacheService->put($cacheKey, $shopId, $data);

            return $data;
        }
    }

    /** @inheritDoc */
    public function getControllers(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'controllers';

        try {
            return $this->createControllersFromData($this->moduleCacheService->get($cacheKey, $shopId));
        } catch (CacheNotFoundException | \JsonException) {
            $data = $this->collectControllersData();
            $this->moduleCacheService->put($cacheKey, $shopId, $data);

            return $this->createControllersFromData($data);
        }
    }

    /** @inheritDoc */
    public function getClassExtensions(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'module_class_extensions';

        try {
            return $this->moduleCacheService->get($cacheKey, $shopId);
        } catch (CacheNotFoundException | \JsonException) {
            $data = $this->activeClassExtensionChainResolver->getActiveExtensionChain($shopId)->getChain();
            $this->moduleCacheService->put($cacheKey, $shopId, $data);

            return $data;
        }
    }

    /** @return array */
    private function collectModulePaths(): array
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
    private function collectControllersData(): array
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
