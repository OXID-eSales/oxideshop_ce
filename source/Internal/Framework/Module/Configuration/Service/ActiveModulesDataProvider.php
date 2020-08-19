<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ActiveModulesDataProvider implements ActiveModulesDataProviderInterface
{
    /**
     * @var ShopConfigurationDaoInterface
     */
    private $shopConfigurationDao;

    /**
     * @var ModuleStateServiceInterface
     */
    private $moduleStateService;

    /**
     * @var ContextInterface
     */
    private $context;

    public function __construct(
        ShopConfigurationDaoInterface $shopConfigurationDao,
        ModuleStateServiceInterface $moduleStateService,
        ContextInterface $context
    ) {
        $this->shopConfigurationDao = $shopConfigurationDao;
        $this->moduleStateService = $moduleStateService;
        $this->context = $context;
    }

    public function getModuleIds(): array
    {
        $moduleIds = [];

        foreach ($this->getActiveModuleConfigurations() as $moduleConfiguration) {
            $moduleIds[] = $moduleConfiguration->getId();
        }

        return $moduleIds;
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
