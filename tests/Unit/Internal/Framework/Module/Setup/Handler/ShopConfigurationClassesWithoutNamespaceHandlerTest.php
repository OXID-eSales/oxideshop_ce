<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Setup\Handler;

use OxidEsales\EshopCommunity\Internal\Framework\Config\Dao\ShopConfigurationSettingDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopConfigurationSetting;
use OxidEsales\EshopCommunity\Internal\Framework\Config\DataObject\ShopSettingType;
use OxidEsales\EshopCommunity\Internal\Framework\Dao\EntryDoesNotExistDaoException;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\{
    ModuleConfiguration,
    ModuleConfiguration\ClassWithoutNamespace
};
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ShopConfigurationClassesWithoutNamespaceHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

/** @internal */
final class ShopConfigurationClassesWithoutNamespaceHandlerTest extends TestCase
{
    use ProphecyTrait;

    public function testHandleOnModuleActivationWithInvalidConfigWillSkipExecution(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $emptyModuleConfig = new ModuleConfiguration();

        (new ShopConfigurationClassesWithoutNamespaceHandler($daoMock->reveal()))
            ->handleOnModuleActivation($emptyModuleConfig, $shopId);

        $daoMock->get(ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES, $shopId)->shouldNotHaveBeenCalled();
        $daoMock->save(Argument::type(ShopConfigurationSetting::class))->shouldNotHaveBeenCalled();
    }

    public function testHandleOnModuleActivationWithSettingNotFoundWillCallSave(): void
    {
        $shopId = 1;
        $moduleId = 'some-module-id';
        $shopClass = 'some-shop-class';
        $moduleClass = 'some-module-class';
        $expectedConfig = [
            $moduleId => [$shopClass => $moduleClass],
            ];
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES, $shopId)
            ->willThrow(EntryDoesNotExistDaoException::class);
        $shopConfig = $this->createEmptyShopConfig($shopId);
        $shopConfig->setValue($expectedConfig);
        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addClassWithoutNamespace(new ClassWithoutNamespace($shopClass, $moduleClass));

        (new ShopConfigurationClassesWithoutNamespaceHandler($daoMock->reveal()))
            ->handleOnModuleActivation($moduleConfig, $shopId);

        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    public function testHandleOnModuleActivationWillSaveMergedConfig(): void
    {
        $shopId = 1;
        $moduleId = 'some-module-id';
        $shopClass1 = 'some-shop-class-1';
        $moduleClass1 = 'some-module-class-1';
        $initialConfig = ['some-key' => 'some-value'];
        $expectedConfig = [
            'some-key' => 'some-value',
            $moduleId => [$shopClass1 => $moduleClass1],
        ];
        $shopConfig = (new ShopConfigurationSetting())->setValue($initialConfig);
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES, $shopId)->willReturn($shopConfig);

        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addClassWithoutNamespace(new ClassWithoutNamespace($shopClass1, $moduleClass1))
            ->addClassWithoutNamespace(new ClassWithoutNamespace('', 'some-module-class-2'))
            ->addClassWithoutNamespace(new ClassWithoutNamespace('some-shop-class-3', ''));

        (new ShopConfigurationClassesWithoutNamespaceHandler($daoMock->reveal()))
            ->handleOnModuleActivation($moduleConfig, $shopId);

        $this->assertSame($expectedConfig, $shopConfig->getValue());
        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    public function testHandleOnModuleDeactivationWithInvalidConfigWillSkipExecution(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $moduleConfig = new ModuleConfiguration();

        (new ShopConfigurationClassesWithoutNamespaceHandler($daoMock->reveal()))
            ->handleOnModuleDeactivation($moduleConfig, $shopId);

        $daoMock->get(ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES, $shopId)->shouldNotHaveBeenCalled();
        $daoMock->save(Argument::type(ShopConfigurationSetting::class))->shouldNotHaveBeenCalled();
    }

    public function testHandleOnModuleDeactivationWithSettingNotFoundWillCallSave(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES, $shopId)
            ->willThrow(EntryDoesNotExistDaoException::class);
        $moduleConfig = (new ModuleConfiguration())
            ->setId('some-module-id')
            ->addClassWithoutNamespace(new ClassWithoutNamespace('some-shop-class', 'some-module-class'));

        (new ShopConfigurationClassesWithoutNamespaceHandler($daoMock->reveal()))
            ->handleOnModuleDeactivation($moduleConfig, $shopId);

        $daoMock->save($this->createEmptyShopConfig($shopId))->shouldHaveBeenCalledOnce();
    }

    public function testHandleOnModuleDeactivationWillSaveCleanedConfig(): void
    {
        $shopId = 1;
        $moduleId = 'some-module-id';
        $initialConfig = [
            'some-key' => 'some-value',
            $moduleId => ['anything'],
            'another-key' => 'another-value',
        ];
        $expectedConfig = [
            'some-key' => 'some-value',
            'another-key' => 'another-value',
        ];
        $shopConfig = (new ShopConfigurationSetting())->setValue($initialConfig);
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES, $shopId)->willReturn($shopConfig);
        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addClassWithoutNamespace(new ClassWithoutNamespace('some-shop-class', 'some-module-class'));

        (new ShopConfigurationClassesWithoutNamespaceHandler($daoMock->reveal()))
            ->handleOnModuleDeactivation($moduleConfig, $shopId);

        $this->assertSame($expectedConfig, $shopConfig->getValue());
        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    private function createEmptyShopConfig(int $shopId): ShopConfigurationSetting
    {
        return (new ShopConfigurationSetting())
            ->setShopId($shopId)
            ->setName(ShopConfigurationSetting::MODULE_CLASSES_WITHOUT_NAMESPACES)
            ->setType(ShopSettingType::ARRAY)
            ->setValue([]);
    }
}
