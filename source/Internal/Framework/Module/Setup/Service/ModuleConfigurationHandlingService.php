<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ModuleConfigurationHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;

class ModuleConfigurationHandlingService implements ModuleConfigurationHandlingServiceInterface
{
    /**
     * @var ModuleConfigurationHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var ModuleConfigurationValidatorInterface[]
     */
    private $moduleConfigurationValidators = [];

    public function handleOnActivation(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        $this->validateModuleConfiguration($moduleConfiguration, $shopId);

        foreach ($this->handlers as $handler) {
            $handler->handleOnModuleActivation($moduleConfiguration, $shopId);
        }
    }

    public function handleOnDeactivation(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        foreach ($this->handlers as $handler) {
            $handler->handleOnModuleDeactivation($moduleConfiguration, $shopId);
        }
    }

    public function addHandler(ModuleConfigurationHandlerInterface $moduleSettingHandler): void
    {
        $this->handlers[] = $moduleSettingHandler;
    }

    public function addValidator(ModuleConfigurationValidatorInterface $configuration): void
    {
        $this->moduleConfigurationValidators[] = $configuration;
    }

    private function validateModuleConfiguration(ModuleConfiguration $moduleConfiguration, int $shopId): void
    {
        foreach ($this->moduleConfigurationValidators as $moduleConfigurationValidator) {
            $moduleConfigurationValidator->validate($moduleConfiguration, $shopId);
        }
    }
}
