<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationPreprocessor;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleLoadSequenceDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Exception\ModuleConfigurationNotFoundException;

final class ModuleConfigurationsSorter implements ShopConfigurationPreprocessorInterface
{
    private ModuleLoadSequenceDaoInterface $moduleLoadSequenceDao;

    public function __construct(
        ModuleLoadSequenceDaoInterface $moduleLoadSequenceDao
    ) {
        $this->moduleLoadSequenceDao = $moduleLoadSequenceDao;
    }

    /** @inheritDoc */
    public function process(int $shopId, array $shopConfiguration): array
    {
        $configuredModulesIds = $this->moduleLoadSequenceDao->get($shopId)->getConfiguredModulesIds();
        if (!$configuredModulesIds) {
            return $shopConfiguration;
        }
        $this->validateModuleLoadSequenceConfiguration($configuredModulesIds, $shopConfiguration['modules']);
        $shopConfiguration['modules'] = $this->rearrangeModuleSequence(\array_reverse($configuredModulesIds), $shopConfiguration['modules']);

        return $shopConfiguration;
    }

    private function validateModuleLoadSequenceConfiguration(array $configuredModulesIds, array $shopModules): void
    {
        foreach ($configuredModulesIds as $moduleId) {
            if (!isset($shopModules[$moduleId])) {
                throw new ModuleConfigurationNotFoundException(
                    "'$moduleId' is defined in module load sequence file, but its configuration was not loaded."
                );
            }
        }
    }

    private function rearrangeModuleSequence(array $moduleLoadSequenceIds, array $shopModules): array
    {
        $linedUpModules = [];
        foreach ($moduleLoadSequenceIds as $moduleId) {
            $linedUpModules[$moduleId] = $shopModules[$moduleId];
            unset($shopModules[$moduleId]);
        }
        return array_merge($shopModules, $linedUpModules);
    }
}
