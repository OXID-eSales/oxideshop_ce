<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ControllersDuplicationModuleConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ControllersValidator;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ControllersValidatorTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testValidationWithCorrectSetting(): void
    {
        $shopAdapter = $this->createStub(ShopAdapterInterface::class);
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'shopControllerName' => 'shopControllerNamespace',
            ]);

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('moduleId');
        $moduleConfiguration->addController(new Controller('alreadyActiveModuleControllerName', 'alreadyActiveModuleControllerNamespace'));

        $shopConfiguration = new ShopConfiguration();
        $shopConfiguration->addModuleConfiguration($moduleConfiguration);

        $shopConfigurationSettingDao = $this->createStub(ShopConfigurationDaoInterface::class);
        $shopConfigurationSettingDao
            ->method('get')
            ->willReturn($shopConfiguration);

        $moduleStateService = $this->createStub(ModuleStateServiceInterface::class);
        $moduleStateService->method('isActive')->willReturn(true);

        $validator = new ControllersValidator(
            $shopAdapter,
            $shopConfigurationSettingDao,
            $this->createStub(LoggerInterface::class),
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addController(
            new Controller(
                'newModuleControllerName',
                'newModuleControllerNamespace'
            )
        );

        $validator->validate($moduleConfiguration, 1);
    }

    public function testValidationWithDuplicatedControllerNamespace(): void
    {
        $this->expectException(ControllersDuplicationModuleConfigurationException::class);

        $shopAdapter = $this->createStub(ShopAdapterInterface::class);
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'anotherModuleControllerId' => 'duplicatedNamespace',
            ]);

        $validator = new ControllersValidator(
            $shopAdapter,
            $this->createStub(ShopConfigurationDaoInterface::class),
            $this->createStub(LoggerInterface::class),
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addController(
            new Controller('someId', 'duplicatedNamespace')
        );

        $validator->validate($moduleConfiguration, 1);
    }

    public function testValidationWithDuplicatedControllerId(): void
    {
        $this->expectException(ControllersDuplicationModuleConfigurationException::class);

        $shopAdapter = $this->createStub(ShopAdapterInterface::class);
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'duplicatedid' => 'anotherModuleNamespace',
            ]);

        $validator = new ControllersValidator(
            $shopAdapter,
            $this->createStub(ShopConfigurationDaoInterface::class),
            $this->createStub(LoggerInterface::class),
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addController(
            new Controller('duplicatedId', 'controllerNamespace')
        );

        $validator->validate($moduleConfiguration, 1);
    }

    public function testValidatorLogsErrorIfModuleControllerAlreadyExistsInControllersMap(): void
    {
        $shopAdapter = $this->createStub(ShopAdapterInterface::class);
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'sameid' => 'sameNamespace',
            ]);

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())->method('error');

        $validator = new ControllersValidator(
            $shopAdapter,
            $this->createStub(ShopConfigurationDaoInterface::class),
            $logger,
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->setId('testModule');
        $moduleConfiguration->addController(
            new Controller('sameId', 'sameNamespace')
        );

        $validator->validate($moduleConfiguration, 1);
    }
}
