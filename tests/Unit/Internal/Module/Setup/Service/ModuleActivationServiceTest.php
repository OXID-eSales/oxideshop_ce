<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Setup\Service;

use OxidEsales\EshopCommunity\Internal\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Provider\ModuleConfigurationProviderInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Service\ModuleActivationService;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Handler\ModuleSettingHandlerInterface;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingNotValidException;
use OxidEsales\EshopCommunity\Internal\Module\Setup\Validator\ModuleSettingValidatorInterface;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ModuleActivationServiceTest extends TestCase
{
    public function testModuleSettingHandlersOnActivation()
    {
        $moduleSetting = new ModuleSetting('testSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting(
            $moduleSetting
        );

        $moduleConfigurationProvider = $this->getMockBuilder(ModuleConfigurationProviderInterface::class)->getMock();
        $moduleConfigurationProvider
            ->method('getModuleConfiguration')
            ->willReturn($moduleConfiguration);

        $moduleSettingHandler = $this->getMockBuilder(ModuleSettingHandlerInterface::class)->getMock();
        $moduleSettingHandler
            ->expects($this->atLeastOnce())
            ->method('canHandle')
            ->with($moduleSetting)
            ->willReturn(true);

        $moduleSettingHandler
            ->expects($this->atLeastOnce())
            ->method('handleOnModuleActivation');

        $moduleActivationService = new ModuleActivationService(
            $moduleConfigurationProvider,
            $this->getMockBuilder(ModuleCacheServiceInterface::class)->getMock()
        );
        $moduleActivationService->addHandler($moduleSettingHandler);

        $moduleActivationService->activate('testModule', 1);
    }

    public function testModuleSettingHandlersOnDeactivation()
    {
        $moduleSetting = new ModuleSetting('testSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting(
            $moduleSetting
        );

        $moduleConfigurationProvider = $this->getMockBuilder(ModuleConfigurationProviderInterface::class)->getMock();
        $moduleConfigurationProvider
            ->method('getModuleConfiguration')
            ->willReturn($moduleConfiguration);

        $moduleSettingHandler = $this->getMockBuilder(ModuleSettingHandlerInterface::class)->getMock();
        $moduleSettingHandler
            ->expects($this->atLeastOnce())
            ->method('canHandle')
            ->with($moduleSetting)
            ->willReturn(true);

        $moduleSettingHandler
            ->expects($this->atLeastOnce())
            ->method('handleOnModuleDeactivation');

        $moduleActivationService = new ModuleActivationService(
            $moduleConfigurationProvider,
            $this->getMockBuilder(ModuleCacheServiceInterface::class)->getMock()
        );
        $moduleActivationService->addHandler($moduleSettingHandler);

        $moduleActivationService->deactivate('testModule', 1);
    }

    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Setup\Exception\ModuleSettingHandlerNotFoundException
     */
    public function testModuleSettingHandlerDoesNotExist()
    {
        $moduleSetting = new ModuleSetting('testSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting(
            $moduleSetting
        );

        $moduleConfigurationProvider = $this->getMockBuilder(ModuleConfigurationProviderInterface::class)->getMock();
        $moduleConfigurationProvider
            ->method('getModuleConfiguration')
            ->willReturn($moduleConfiguration);

        $moduleActivationService = new ModuleActivationService(
            $moduleConfigurationProvider,
            $this->getMockBuilder(ModuleCacheServiceInterface::class)->getMock()
        );

        $moduleActivationService->activate('testModule', 1);
    }

    public function testModuleSettingInvalid()
    {
        $this->expectException(ModuleSettingNotValidException::class);

        $moduleSetting = new ModuleSetting('testSetting', 'value');

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addSetting(
            $moduleSetting
        );

        $moduleSettingValidator = $this->getMockBuilder(ModuleSettingValidatorInterface::class)->getMock();
        $moduleSettingValidator
            ->method('canValidate')
            ->willReturn(true);
        $moduleSettingValidator
            ->method('validate')
            ->willThrowException(new ModuleSettingNotValidException());

        $moduleConfigurationProvider = $this->getMockBuilder(ModuleConfigurationProviderInterface::class)->getMock();
        $moduleConfigurationProvider
            ->method('getModuleConfiguration')
            ->willReturn($moduleConfiguration);

        $moduleActivationService = new ModuleActivationService(
            $moduleConfigurationProvider,
            $this->getMockBuilder(ModuleCacheServiceInterface::class)->getMock()
        );

        $moduleActivationService->addValidator($moduleSettingValidator);

        $moduleActivationService->activate('testModule', 1);
    }
}
