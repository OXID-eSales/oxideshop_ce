<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleDependencyDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\UnresolvedModuleDependencies;

use function in_array;

class ModuleDependencyResolver implements ModuleDependencyResolverInterface
{
    public function __construct(
        private readonly ModuleDependencyDaoInterface $moduleDependencyDao,
        private readonly ModuleConfigurationDaoInterface $moduleConfigurationDao
    ) {
    }

    public function getUnresolvedActivationDependencies(string $moduleId, int $shopId): UnresolvedModuleDependencies
    {
        $unresolvedDependencies = new UnresolvedModuleDependencies();
        $requiredModules = $this->moduleDependencyDao->get($moduleId)->getRequiredModuleIds();
        $activeModules = $this->getActiveModuleIds($shopId);

        foreach ($requiredModules as $requiredModule) {
            if (!in_array($requiredModule, $activeModules, true)) {
                $unresolvedDependencies->addModuleId($requiredModule);
            }
        }

        return $unresolvedDependencies;
    }

    public function getUnresolvedDeactivationDependencies(string $moduleId, int $shopId): UnresolvedModuleDependencies
    {
        $unresolvedDependencies = new UnresolvedModuleDependencies();
        $activeModuleIds = $this->getActiveModuleIds($shopId);

        foreach ($activeModuleIds as $activeModuleId) {
            if ($this->isRequiredByActiveModule($moduleId, $activeModuleId)) {
                $unresolvedDependencies->addModuleId($activeModuleId);
            }
        }

        return $unresolvedDependencies;
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

    private function isRequiredByActiveModule(string $moduleId, string $activeModule): bool
    {
        return
            $moduleId !== $activeModule &&
            $this->moduleDependencyDao->get($activeModule)->isRequiredModule($moduleId);
    }
}
