<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ModulesDataProvider implements ModulesDataProviderInterface
{
    public function __construct(private ShopConfigurationDaoInterface $shopConfigurationDao, private ModulePathResolverInterface $modulePathResolver, private ContextInterface $context)
    {
    }

    /** @inheritDoc */
    public function getModuleIds(): array
    {
        $moduleIds = [];
        $shopId = $this->context->getCurrentShopId();
        $moduleConfigurations = $this->shopConfigurationDao->get($shopId)->getModuleConfigurations();

        foreach ($moduleConfigurations as $moduleConfiguration) {
            $moduleIds[] = $moduleConfiguration->getId();
        }

        return $moduleIds;
    }

    /** @inheritDoc */
    public function getModulePaths(): array
    {
        $shopId = $this->context->getCurrentShopId();

        $modulePaths = [];
        $moduleConfigurations = $this->shopConfigurationDao->get($shopId)->getModuleConfigurations();

        foreach ($moduleConfigurations as $moduleConfiguration) {
            $modulePaths[] = $this->modulePathResolver->getFullModulePathFromConfiguration(
                $moduleConfiguration->getId(),
                $this->context->getCurrentShopId()
            );
        }

        return $modulePaths;
    }
}
