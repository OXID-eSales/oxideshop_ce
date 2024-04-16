<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Validator;

use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Exception\ControllersDuplicationModuleConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Validator\ControllersValidator;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration\Controller;
use Psr\Log\LoggerInterface;

/**
 * @internal
 */
final class ControllersValidatorTest extends TestCase
{
    #[DoesNotPerformAssertions]
    public function testValidationWithCorrectSetting(): void
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
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

        $shopConfigurationSettingDao = $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock();
        $shopConfigurationSettingDao
            ->method('get')
            ->willReturn($shopConfiguration);

        $moduleStateService = $this->getMockBuilder(ModuleStateServiceInterface::class)->getMock();
        $moduleStateService->method('isActive')->willReturn(true);

        $validator = new ControllersValidator(
            $shopAdapter,
            $shopConfigurationSettingDao,
            $this->getMockBuilder(LoggerInterface::class)->getMock(),
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

        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'anotherModuleControllerId' => 'duplicatedNamespace',
            ]);

        $validator = new ControllersValidator(
            $shopAdapter,
            $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock(),
            $this->getMockBuilder(LoggerInterface::class)->getMock(),
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

        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'duplicatedid' => 'anotherModuleNamespace',
            ]);

        $validator = new ControllersValidator(
            $shopAdapter,
            $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock(),
            $this->getMockBuilder(LoggerInterface::class)->getMock(),
        );

        $moduleConfiguration = new ModuleConfiguration();
        $moduleConfiguration->addController(
            new Controller('duplicatedId', 'controllerNamespace')
        );

        $validator->validate($moduleConfiguration, 1);
    }

    public function testValidatorLogsErrorIfModuleControllerAlreadyExistsInControllersMap(): void
    {
        $shopAdapter = $this->getMockBuilder(ShopAdapterInterface::class)->getMock();
        $shopAdapter
            ->method('getShopControllerClassMap')
            ->willReturn([
                'sameid' => 'sameNamespace',
            ]);

        $logger = $this->getMockBuilder(LoggerInterface::class)->getMock();
        $logger->expects($this->once())->method('error');

        $validator = new ControllersValidator(
            $shopAdapter,
            $this->getMockBuilder(ShopConfigurationDaoInterface::class)->getMock(),
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
