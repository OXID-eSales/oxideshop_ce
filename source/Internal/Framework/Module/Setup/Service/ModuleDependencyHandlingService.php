<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSetupException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;

final class ModuleDependencyHandlingService
{
    /** @var array */
    private $moduleIdToRequiredActiveModuleIds = [];

    /** @var array */
    private $moduleIdToRequiredBy = [];

    /**
     * @param ModuleDependencyServiceInterface[] $dependencies
     */
    public function __construct(
        iterable $moduleDependencies,
        ModuleStateServiceInterface $moduleStateService
    ) {
        $this->moduleStateService = $moduleStateService;

        foreach ($moduleDependencies as $dependency) {
           $this->registerDependency($dependency);
        }
    }

    public function canActivateModule(string $moduleId, int $shopId): void
    {
        if (!isset($this->moduleIdToRequiredActiveModuleIds[$moduleId])) {
            return;
        }

        foreach ($this->moduleIdToRequiredActiveModuleIds[$moduleId] as $requiredActiveModuleId) {
            if (!$this->moduleStateService->isActive($requiredActiveModuleId, $shopId)) {
                throw new ModuleSetupException(
                    'Cannot activate module with id "' . $moduleId .
                    '" as it needs active module with id "' . $requiredActiveModuleId . '" in shop ' . $shopId
                );
            }
        }
    }

    public function canDeactivateModule(string $moduleId, int $shopId): void
    {
        if (!isset($this->moduleIdToRequiredBy[$moduleId])) {
            return;
        }

        foreach ($this->moduleIdToRequiredBy[$moduleId] as $requiredActiveByModuleId) {
            if ($this->moduleStateService->isActive($requiredActiveByModuleId, $shopId)) {
                throw new ModuleSetupException(
                    'Cannot deactivate module with id "' . $moduleId .
                    '" as it is required by active module with id "' . $requiredActiveByModuleId . '" in shop ' . $shopId
                );
            }
        }
    }

    private function registerDependency(ModuleDependencyServiceInterface $dependency): void
    {
        $this->moduleIdToRequiredActiveModuleIds[$dependency->getModuleId()] =
            array_keys($dependency->getDependencies());

        foreach ($dependency->getDependencies() as $moduleId => $dependId) {
            $this->moduleIdToRequiredBy[$moduleId][$dependId] = $dependId;
        }
    }
}
