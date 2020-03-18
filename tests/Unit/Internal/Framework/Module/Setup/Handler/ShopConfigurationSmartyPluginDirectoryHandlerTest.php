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
    ModuleConfiguration\SmartyPluginDirectory
};
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\ShopConfigurationSmartyPluginDirectoryHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/** @internal */
final class ShopConfigurationSmartyPluginDirectoryHandlerTest extends TestCase
{
    public function testHandleOnModuleActivationWithInvalidConfigWillSkipExecution(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $emptyModuleConfig = new ModuleConfiguration();

        (new ShopConfigurationSmartyPluginDirectoryHandler($daoMock->reveal()))
            ->handleOnModuleActivation($emptyModuleConfig, $shopId);

        $daoMock->get(ShopConfigurationSetting::MODULE_SMARTY_PLUGIN_DIRECTORIES, $shopId)->shouldNotHaveBeenCalled();
        $daoMock->save(Argument::type(ShopConfigurationSetting::class))->shouldNotHaveBeenCalled();
    }

    public function testHandleOnModuleActivationWithSettingNotFoundWillCallSave(): void
    {
        $shopId = 1;
        $moduleId = 'some-module-id';
        $dir = 'some-dir';
        $expectedConfig = [
            $moduleId => [$dir],
        ];
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_SMARTY_PLUGIN_DIRECTORIES, $shopId)
            ->willThrow(EntryDoesNotExistDaoException::class);
        $shopConfig = $this->createEmptyShopConfig($shopId);
        $shopConfig->setValue($expectedConfig);
        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addSmartyPluginDirectory(new SmartyPluginDirectory($dir));

        (new ShopConfigurationSmartyPluginDirectoryHandler($daoMock->reveal()))
            ->handleOnModuleActivation($moduleConfig, $shopId);

        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    public function testHandleOnModuleActivationWillSaveMergedConfig(): void
    {
        $shopId = 1;
        $moduleId = 'some-module-id';
        $dir1 = 'some-dir-1';
        $dir2 = 'some-dir-2';
        $initialConfig = ['some-key' => 'some-value'];
        $expectedConfig = [
            'some-key' => 'some-value',
            $moduleId => [$dir1, $dir2],
        ];
        $shopConfig = (new ShopConfigurationSetting())->setValue($initialConfig);
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_SMARTY_PLUGIN_DIRECTORIES, $shopId)->willReturn($shopConfig);
        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addSmartyPluginDirectory(new SmartyPluginDirectory($dir1))
            ->addSmartyPluginDirectory(new SmartyPluginDirectory($dir2));

        (new ShopConfigurationSmartyPluginDirectoryHandler($daoMock->reveal()))
            ->handleOnModuleActivation($moduleConfig, $shopId);

        $this->assertSame($expectedConfig, $shopConfig->getValue());
        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    public function testHandleOnModuleDeactivationWithInvalidConfigWillSkipExecution(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $moduleConfig = new ModuleConfiguration();

        (new ShopConfigurationSmartyPluginDirectoryHandler($daoMock->reveal()))
            ->handleOnModuleDeactivation($moduleConfig, $shopId);

        $daoMock->get(ShopConfigurationSetting::MODULE_SMARTY_PLUGIN_DIRECTORIES, $shopId)->shouldNotHaveBeenCalled();
        $daoMock->save(Argument::type(ShopConfigurationSetting::class))->shouldNotHaveBeenCalled();
    }

    public function testHandleOnModuleDeactivationWithSettingNotFoundWillCallSave(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_SMARTY_PLUGIN_DIRECTORIES, $shopId)
            ->willThrow(EntryDoesNotExistDaoException::class);
        $moduleConfig = (new ModuleConfiguration())
            ->setId('some-module-id')
            ->addSmartyPluginDirectory(new SmartyPluginDirectory('some-dir'));

        (new ShopConfigurationSmartyPluginDirectoryHandler($daoMock->reveal()))
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
        $daoMock->get(ShopConfigurationSetting::MODULE_SMARTY_PLUGIN_DIRECTORIES, $shopId)->willReturn($shopConfig);
        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addSmartyPluginDirectory(new SmartyPluginDirectory('some-dir'));

        (new ShopConfigurationSmartyPluginDirectoryHandler($daoMock->reveal()))
            ->handleOnModuleDeactivation($moduleConfig, $shopId);

        $this->assertSame($expectedConfig, $shopConfig->getValue());
        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    private function createEmptyShopConfig(int $shopId): ShopConfigurationSetting
    {
        return (new ShopConfigurationSetting())
            ->setShopId($shopId)
            ->setName(ShopConfigurationSetting::MODULE_SMARTY_PLUGIN_DIRECTORIES)
            ->setType(ShopSettingType::ARRAY)
            ->setValue([]);
    }
}
