<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleConfigurationHandlingService;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ModuleConfigurationHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ModuleSettingValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleConfigurationHandlingServiceTest extends TestCase
{
    public function testHandlingOnActivation()
    {
        $moduleSetting = new ModuleSetting('testSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addSetting($moduleSetting);

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
        $moduleSetting = new ModuleSetting('testSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addSetting($moduleSetting);

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
        $moduleConfiguration->addSetting(
            new ModuleSetting('testSetting', 'value')
        );

        $moduleSettingValidator = $this->getMockBuilder(ModuleSettingValidatorInterface::class)->getMock();
        $moduleSettingValidator
            ->method('canValidate')
            ->willReturn(true);
        $moduleSettingValidator
            ->method('validate')
            ->willThrowException(new ModuleSettingNotValidException());

        $moduleSettingsHandlingService = new ModuleConfigurationHandlingService();

        $moduleSettingsHandlingService->addValidator($moduleSettingValidator);

        $moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, 1);
    }
}
