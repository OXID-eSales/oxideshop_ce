<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service;

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
        $modulePaths = [];

        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            $shopId = $this->context->getCurrentShopId();
            $moduleId = $moduleConfiguration->getId();

            if (!$this->moduleCacheService->exists($moduleId, $shopId)) {
                $modulePaths[] = $this->modulePathResolver->getFullModulePathFromConfiguration(
                    $moduleId,
                    $shopId
                );

                $this->moduleCacheService->put($moduleId, $shopId, $modulePaths);
            } else {
                $modulePaths[] = $this->moduleCacheService->get($moduleId, $shopId);
            }
        }

        return $modulePaths;
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
