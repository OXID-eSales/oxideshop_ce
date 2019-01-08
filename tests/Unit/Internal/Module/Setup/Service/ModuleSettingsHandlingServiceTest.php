<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleSettingsHandlingService;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ModuleSettingHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ModuleSettingValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleSettingsHandlingServiceTest extends TestCase
{
    public function testModuleSettingHandlersOnActivation()
    {
        $moduleSetting = new ModuleSetting('testSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addSetting($moduleSetting);

        $moduleSettingHandler = $this->getMockBuilder(ModuleSettingHandlerInterface::class)->getMock();
        $moduleSettingHandler
            ->expects($this->atLeastOnce())
            ->method('canHandle')
            ->with($moduleSetting)
            ->willReturn(true);

        $moduleSettingHandler
            ->expects($this->atLeastOnce())
            ->method('handleOnModuleActivation');

        $moduleSettingsHandlingService = new ModuleSettingsHandlingService();
        $moduleSettingsHandlingService->addHandler($moduleSettingHandler);

        $moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, 1);
    }

    public function testModuleSettingHandlersOnDeactivation()
    {
        $moduleSetting = new ModuleSetting('testSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addSetting($moduleSetting);

        $moduleSettingHandler = $this->getMockBuilder(ModuleSettingHandlerInterface::class)->getMock();
        $moduleSettingHandler
            ->expects($this->atLeastOnce())
            ->method('canHandle')
            ->with($moduleSetting)
            ->willReturn(true);

        $moduleSettingHandler
            ->expects($this->atLeastOnce())
            ->method('handleOnModuleDeactivation');

        $moduleSettingsHandlingService = new ModuleSettingsHandlingService();
        $moduleSettingsHandlingService->addHandler($moduleSettingHandler);

        $moduleSettingsHandlingService->handleOnDeactivation($moduleConfiguration, 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingHandlerNotFoundException
     */
    public function testModuleSettingHandlerDoesNotExist()
    {
        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting(
            new ModuleSetting('testSetting', 'value')
        );

        $moduleSettingsHandlingService = new ModuleSettingsHandlingService();

        $moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, 1);
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

        $moduleSettingsHandlingService = new ModuleSettingsHandlingService();

        $moduleSettingsHandlingService->addValidator($moduleSettingValidator);

        $moduleSettingsHandlingService->handleOnActivation($moduleConfiguration, 1);
    }
}
