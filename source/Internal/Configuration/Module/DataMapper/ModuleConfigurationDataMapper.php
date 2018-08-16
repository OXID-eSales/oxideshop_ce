<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper;

use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataMapper\Validator\SettingValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ModuleSetting;

/**
 * @internal
 */
class ModuleConfigurationDataMapper implements ModuleConfigurationDataMapperInterface
{
    /**
     * @var SettingValidatorInterface
     */
    private $settingValidator;

    /**
     * ModuleConfigurationDataMapper constructor.
     * @param SettingValidatorInterface $settingValidator
     */
    public function __construct(SettingValidatorInterface $settingValidator)
    {
        $this->settingValidator = $settingValidator;
    }

    /**
     * @param ModuleConfiguration $configuration
     * @return array
     */
    public function toData(ModuleConfiguration $configuration): array
    {
        // TODO: Implement toData() method.
    }

    /**
     * @param array $data
     * @return ModuleConfiguration
     */
    public function fromData(array $data): ModuleConfiguration
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration
            ->setVersion($data['version'])
            ->setState($data['state']);

        if (isset($data['settings'])) {
            $this->setSettings($moduleConfiguration, $data['settings']);
        }

        return $moduleConfiguration;
    }

    /**
     * @param ModuleConfiguration $moduleConfiguration
     * @param array               $settingsData
     */
    private function setSettings(ModuleConfiguration $moduleConfiguration, array $settingsData)
    {
        foreach ($settingsData as $settingName => $settingValue) {
            $setting = new ModuleSetting($settingName, $settingValue);

            $this->settingValidator->validate(
                $moduleConfiguration->getVersion(),
                $setting
            );

            $moduleConfiguration->setModuleSetting($settingName, $setting);
        }
    }
}
