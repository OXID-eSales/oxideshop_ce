<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleConfigurationHandlingService;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ModuleConfigurationHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ModuleSettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ModuleConfigurationValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationHandlingServiceTest extends TestCase
{
    public function testHandlingOnActivation()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');

        $setting = new Setting();
        $setting
            ->setName('testSetting')
            ->setValue('value');

        $moduleConfiguration->addModuleSetting($setting);

        $handler = $this->getMockBuilder(ModuleConfigurationHandlerInterface::class)->getMock();
        $handler
            ->expects($this->atLeastOnce())
            ->method('handleOnModuleActivation');

        $moduleSettingsHandlingService = new ModuleConfigurationHandlingService();
        $moduleSettingsHandlingService->addHandler($handler);

        $moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, 1);
    }

    public function testHandlingOnDeactivation()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $setting = new Setting();
        $setting
            ->setName('testSetting')
            ->setValue('value');

        $moduleConfiguration->addModuleSetting($setting);


        $handler = $this->getMockBuilder(ModuleConfigurationHandlerInterface::class)->getMock();

        $handler
            ->expects($this->atLeastOnce())
            ->method('handleOnModuleDeactivation');

        $moduleSettingsHandlingService = new ModuleConfigurationHandlingService();
        $moduleSettingsHandlingService->addHandler($handler);

        $moduleSettingsHandlingService->handleOnDeactivation($moduleConfiguration, 1);
    }

    public function testModuleSettingInvalid()
    {
        $this->expectException(ModuleSettingNotValidException::class);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $setting = new Setting();
        $setting
            ->setName('testSetting')
            ->setValue('value');

        $moduleConfiguration->addModuleSetting($setting);

        $moduleConfigurationValidator = $this->getMockBuilder(ModuleConfigurationValidatorInterface::class)->getMock();
        $moduleConfigurationValidator
            ->method('validate')
            ->willThrowException(new ModuleSettingNotValidException());

        $moduleSettingsHandlingService = new ModuleConfigurationHandlingService();

        $moduleSettingsHandlingService->addValidator($moduleConfigurationValidator);

        $moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, 1);
    }
}
