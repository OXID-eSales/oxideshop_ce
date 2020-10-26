<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ActiveModulesDataProvider implements ActiveModulesDataProviderInterface
{
    /** @var ShopConfigurationDaoInterface */
    private $shopConfigurationDao;
    /** @var ModuleStateServiceInterface */
    private $moduleStateService;
    /** @var ContextInterface */
    private $context;
    /** @var ModulePathResolverInterface */
    private $modulePathResolver;
    /** @var ModuleCacheServiceInterface */
    private $moduleCacheService;

    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ModuleStateServiceInterface $moduleStateService,
        ModulePathResolverInterface $modulePathResolver,
        ContextInterface $context,
        ModuleCacheServiceInterface $moduleCacheService
    ) {
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->moduleStateService = $moduleStateService;
        $this->modulePathResolver = $modulePathResolver;
        $this->context = $context;
        $this->moduleCacheService = $moduleCacheService;
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

        if (!$this->moduleCacheService->exists($cacheKey, $shopId)) {
            $modulePaths = [];
            foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
                $modulePaths[$moduleConfiguration->getId()] = $this
                    ->modulePathResolver
                    ->getFullModulePathFromConfiguration(
                        $moduleConfiguration->getId(),
                        $this->context->getCurrentShopId()
                    );
            }

            $this->moduleCacheService->put($cacheKey, $shopId, $modulePaths);
        }

        return $this->moduleCacheService->get($cacheKey, $shopId);
    }

    public function getTemplates(): array
    {
        $shopId = $this->context->getCurrentShopId();
        $cacheKey = 'templates';

        if (!$this->moduleCacheService->exists($cacheKey, $shopId)) {
            $templates = [];
            foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
                $templates[$moduleConfiguration->getId()] = $moduleConfiguration->getTemplates();
            }

            $this->moduleCacheService->put($cacheKey, $shopId, $templates);
        }

        return $this->moduleCacheService->get($cacheKey, $shopId);
    }

    private function getActiveModuleConfigurations(): array
    {
        $moduleConfigurations = [];
        $shopId = $this->context->getCurrentShopId();

        foreach ($this->shopConfigurationDao->get($shopId)->getModuleConfigurations() as $moduleConfiguration) {
            if ($this->moduleStateService->isActive($moduleConfiguration->getId(), $shopId)) {
                $moduleConfigurations[] = $moduleConfiguration;
            }
        }

        return $moduleConfigurations;
    }
}
