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
    ModuleConfiguration\Template
};
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Handler\TemplatesModuleSettingHandler;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

/** @internal */
final class TemplatesModuleSettingHandlerTest extends TestCase
{
    public function testHandleOnModuleActivationWithInvalidConfigWillSkipExecution(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $emptyModuleConfig = new ModuleConfiguration();

        (new TemplatesModuleSettingHandler($daoMock->reveal()))
            ->handleOnModuleActivation($emptyModuleConfig, $shopId);

        $daoMock->get(ShopConfigurationSetting::MODULE_TEMPLATES, $shopId)->shouldNotHaveBeenCalled();
        $daoMock->save(Argument::type(ShopConfigurationSetting::class))->shouldNotHaveBeenCalled();
    }

    public function testHandleOnModuleActivationWithSettingNotFoundWillCallSave(): void
    {
        $shopId = 1;
        $moduleId = 'some-module-id';
        $tplKey = 'some-tpl-key';
        $tplPath = 'some-tpl-dir';
        $expectedConfig = [
            $moduleId => [
                $tplKey => $tplPath,
            ],
        ];
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_TEMPLATES, $shopId)
            ->willThrow(EntryDoesNotExistDaoException::class);
        $shopConfig = $this->createEmptyShopConfig($shopId);
        $shopConfig->setValue($expectedConfig);
        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addTemplate(new Template($tplKey, $tplPath));

        (new TemplatesModuleSettingHandler(
            $daoMock->reveal()
        ))->handleOnModuleActivation($moduleConfig, $shopId);

        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    public function testHandleOnModuleActivationWillSaveMergedConfig(): void
    {
        $shopId = 1;
        $moduleId = 'some-module-id';
        $tplKey1 = 'some-tpl-key-1';
        $tplKey2 = 'some-tpl-key-2';
        $tplPath1 = 'some-tpl-dir-1';
        $tplPath2 = 'some-tpl-dir-2';
        $initialConfig = ['some-key' => 'some-value'];
        $expectedConfig = [
            'some-key' => 'some-value',
            $moduleId => [
                $tplKey1 => $tplPath1,
                $tplKey2 => $tplPath2,
            ],
        ];
        $shopConfig = (new ShopConfigurationSetting())->setValue($initialConfig);
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_TEMPLATES, $shopId)->willReturn($shopConfig);
        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addTemplate(new Template($tplKey1, $tplPath1))
            ->addTemplate(new Template($tplKey2, $tplPath2));

        (new TemplatesModuleSettingHandler($daoMock->reveal()))
            ->handleOnModuleActivation($moduleConfig, $shopId);

        $this->assertSame($expectedConfig, $shopConfig->getValue());
        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    public function testHandleOnModuleDeactivationWithInvalidConfigWillSkipExecution(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $moduleConfig = new ModuleConfiguration();

        (new TemplatesModuleSettingHandler($daoMock->reveal()))
            ->handleOnModuleDeactivation($moduleConfig, $shopId);

        $daoMock->get(ShopConfigurationSetting::MODULE_TEMPLATES, $shopId)->shouldNotHaveBeenCalled();
        $daoMock->save(Argument::type(ShopConfigurationSetting::class))->shouldNotHaveBeenCalled();
    }

    public function testHandleOnModuleDeactivationWithSettingNotFoundWillCallSave(): void
    {
        $shopId = 1;
        $daoMock = $this->prophesize(ShopConfigurationSettingDaoInterface::class);
        $daoMock->get(ShopConfigurationSetting::MODULE_TEMPLATES, $shopId)
            ->willThrow(EntryDoesNotExistDaoException::class);
        $moduleConfig = (new ModuleConfiguration())
            ->setId('some-module-id')
            ->addTemplate(new Template('some-tpl-key', 'some-tpl-path'));

        (new TemplatesModuleSettingHandler($daoMock->reveal()))
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
        $daoMock->get(ShopConfigurationSetting::MODULE_TEMPLATES, $shopId)->willReturn($shopConfig);
        $moduleConfig = (new ModuleConfiguration())
            ->setId($moduleId)
            ->addTemplate(new Template('some-tpl-key', 'some-tpl-dir'));

        (new TemplatesModuleSettingHandler($daoMock->reveal()))
            ->handleOnModuleDeactivation($moduleConfig, $shopId);

        $this->assertSame($expectedConfig, $shopConfig->getValue());
        $daoMock->save($shopConfig)->shouldHaveBeenCalledOnce();
    }

    private function createEmptyShopConfig(int $shopId): ShopConfigurationSetting
    {
        return (new ShopConfigurationSetting())
            ->setShopId($shopId)
            ->setName(ShopConfigurationSetting::MODULE_TEMPLATES)
            ->setType(ShopSettingType::ARRAY)
            ->setValue([]);
    }
}
