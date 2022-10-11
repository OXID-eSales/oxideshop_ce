<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;

class ModuleConfigurationHandlingService implements ModuleConfigurationHandlingServiceInterface
{
    /**
     * @var ModuleConfigurationValidatorInterface[]
     */
    private array $moduleConfigurationValidators = [];

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    public function handleOnActivation(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        $this->validateModuleConfiguration($moduleConfiguration, $shopId);
    }

    /**
     * @param ModuleConfigurationValidatorInterface $configuration
     */
    public function addValidator(ModuleConfigurationValidatorInterface $configuration): void
    {
        $this->moduleConfigurationValidators[] = $configuration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    private function validateModuleConfiguration(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        foreach ($this->moduleConfigurationValidators as $moduleConfigurationValidator) {
            $moduleConfigurationValidator->validate($moduleConfiguration, $shopId);
        }
    }
}
