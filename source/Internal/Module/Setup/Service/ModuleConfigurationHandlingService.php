<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ModuleConfigurationHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ModuleSettingValidatorInterface;

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
     * @var array
     */
    private $moduleSettingValidators = [];

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    public function handleOnActivation(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        $this->validateModuleSettings($moduleConfiguration, $shopId);

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
     * @param ModuleSettingValidatorInterface $moduleSettingValidator
     */
    public function addValidator(ModuleSettingValidatorInterface $moduleSettingValidator)
    {
        $this->moduleSettingValidators[] = $moduleSettingValidator;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param int                 $shopId
     */
    private function validateModuleSettings(ModuleConfiguration $moduleConfiguration, int $shopId)
    {
        foreach ($moduleConfiguration->getSettings() as $setting) {
            foreach ($this->moduleSettingValidators as $validator) {
                if ($validator->canValidate($setting)) {
                    $validator->validate($setting, $moduleConfiguration->getId(), $shopId);
                }
            }
        }
    }
}
