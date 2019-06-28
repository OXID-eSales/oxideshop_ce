<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ModuleConfigurationHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ModuleConfigurationValidatorInterface;

/**
 * @internal
 */
class ModuleConfigurationHandlingService implements ModuleConfigurationHandlingServiceInterface
{
    /**
     * @var ModuleConfigurationHandlerInterface[]
     */
    private $handlers = [];

    /**
     * @var ModuleConfigurationValidatorInterface[]
     */
    private $moduleConfigurationValidator = [];

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    public function handleOnActivation(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        $this->validateModuleConfiguration($moduleConfiguration, $shopId);

        foreach ($this->handlers as $handler) {
            $handler->handleOnModuleActivation($moduleConfiguration, $shopId);
        }
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    public function handleOnDeactivation(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        foreach ($this->handlers as $handler) {
            $handler->handleOnModuleDeactivation($moduleConfiguration, $shopId);
        }
    }

    /**
     * @param ModuleConfigurationHandlerInterface $moduleSettingHandler
     */
    public function addHandler(ModuleConfigurationHandlerInterface $moduleSettingHandler)
    {
        $this->handlers[] = $moduleSettingHandler;
    }

    /**
     * @param ModuleConfigurationValidatorInterface $configuration
     */
    public function addValidator(ModuleConfigurationValidatorInterface $configuration)
    {
        $this->moduleConfigurationValidator[] = $configuration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    private function validateModuleConfiguration(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        foreach ($moduleConfiguration->getSettings() as $setting) {
            foreach ($this->moduleConfigurationValidator as $validator) {
                if ($moduleConfiguration->hasSetting($setting->getName())) {
                    $validator->validate($moduleConfiguration, $shopId);
                }
            }
        }
    }
}
