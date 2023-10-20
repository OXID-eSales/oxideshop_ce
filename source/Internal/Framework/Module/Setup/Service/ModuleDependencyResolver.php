<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleDependencyDaoInterface;

use function in_array;

class ModuleDependencyResolver implements ModuleDependencyResolverInterface
{
    public function __construct(
        private readonly ModuleDependencyDaoInterface $moduleDependencyDao,
        private readonly ModuleConfigurationDaoInterface $moduleConfigurationDao
    ) {
    }

    public function canActivateModule(string $moduleId, int $shopId): bool
    {
        $requiredModules = $this->moduleDependencyDao->get($moduleId)->getRequiredModuleIds();
        $activeModules = $this->getActiveModuleIds($shopId);

        foreach ($requiredModules as $requiredModule) {
            if (!in_array($requiredModule, $activeModules, true)) {
                return false;
            }
        }

        return true;
    }

    public function canDeactivateModule(string $moduleId, int $shopId): bool
    {
        $activeModules = $this->getActiveModuleIds($shopId);

        foreach ($activeModules as $activeModule) {
            if ($this->isDeactivatable($moduleId, $activeModule)) {
                return false;
            }
        }

        return true;
    }

    private function getActiveModuleIds(int $shopId): array
    {
        $activeModuleIds = [];

        foreach ($this->moduleConfigurationDao->getAll($shopId) as $moduleConfiguration) {
            if ($moduleConfiguration->isActivated()) {
                $activeModuleIds[] = $moduleConfiguration->getId();
            }
        }

        return $activeModuleIds;
    }

    private function isDeactivatable(string $moduleId, string $activeModule): bool
    {
        return
            $moduleId !== $activeModule &&
            $this->moduleDependencyDao->get($activeModule)->isRequiredModule($moduleId);
    }
}
