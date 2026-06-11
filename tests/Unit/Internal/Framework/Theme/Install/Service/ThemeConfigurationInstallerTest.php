<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Provider\ThemeConfigurationProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Exception\ThemeConfigurationInstallException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstaller;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationMerger;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;

final class ThemeConfigurationInstallerTest extends TestCase
{
    private string $themePath = '/some/theme/path';

    public function testInstallSavesConfigurationForEachShop(): void
    {
        $savedShopIds = [];
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(false);
        $dao->method('save')->willReturnCallback(function (ThemeConfiguration $config, int $shopId) use (&$savedShopIds) {
            $savedShopIds[] = $shopId;
        });

        $this->createInstaller($dao, shopIds: [1, 2, 3])->install($this->themePath);

        $this->assertSame([1, 2, 3], $savedShopIds);
    }

    public function testInstallDoesNotLeakConfigurationBetweenShops(): void
    {
        $shop1Existing = $this->buildConfiguration(settingValues: ['testSetting' => 'shop1CustomValue']);
        $shop2Existing = $this->buildConfiguration(settingValues: []);

        $savedConfigs = [];
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(true);
        $dao->method('get')->willReturnOnConsecutiveCalls($shop1Existing, $shop2Existing);
        $dao->method('save')->willReturnCallback(function (ThemeConfiguration $config, int $shopId) use (&$savedConfigs) {
            $savedConfigs[$shopId] = $config;
        });

        $this->createInstaller($dao, shopIds: [1, 2])->install($this->themePath);

        $this->assertSettingValue('shop1CustomValue', 'testSetting', $savedConfigs[1]);
        $this->assertSettingValue('defaultValue', 'testSetting', $savedConfigs[2]);
    }

    public function testInstallThrowsWhenShopFails(): void
    {
        $this->expectException(ThemeConfigurationInstallException::class);

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturn(false);
        $dao->method('save')->willThrowException(new \RuntimeException('permission denied'));

        $this->createInstaller($dao, shopIds: [1])->install($this->themePath);
    }

    public function testUninstallThrowsWhenShopFails(): void
    {
        $this->expectException(ThemeConfigurationInstallException::class);

        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('delete')->willThrowException(new \RuntimeException('permission denied'));

        $this->createInstaller($dao, shopIds: [1])->uninstall($this->themePath);
    }

    public function testIsInstalledReturnsFalseWhenNoShopsExist(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);

        $this->assertFalse(
            $this->createInstaller($dao, shopIds: [])->isInstalled($this->themePath)
        );
    }

    public function testIsInstalledReturnsFalseWhenMissingForAnyShop(): void
    {
        $dao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $dao->method('exists')->willReturnOnConsecutiveCalls(true, false);

        $this->assertFalse(
            $this->createInstaller($dao, shopIds: [1, 2])->isInstalled($this->themePath)
        );
    }

    private function createInstaller(
        ThemeConfigurationDaoInterface $dao,
        array $shopIds = [1],
    ): ThemeConfigurationInstaller {
        $metaData = (new ThemeMetaData())->setId('testTheme');

        $metaDataProvider = $this->createStub(ThemeMetaDataProviderInterface::class);
        $metaDataProvider->method('get')->willReturn($metaData);

        $defaultSetting = (new Setting())->setName('testSetting')->setType('str')->setValue('defaultValue');
        $defaultConfiguration = (new ThemeConfiguration())->addThemeSetting($defaultSetting);

        $configurationProvider = $this->createStub(ThemeConfigurationProviderInterface::class);
        $configurationProvider->method('get')->willReturn($defaultConfiguration);

        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getAllShopIds')->willReturn($shopIds);
        $context->method('getShopRootPath')->willReturn('/shop/root');

        return new ThemeConfigurationInstaller(
            $metaDataProvider,
            $configurationProvider,
            $dao,
            new ThemeConfigurationMerger(),
            $context,
        );
    }

    private function buildConfiguration(array $settingValues): ThemeConfiguration
    {
        $config = (new ThemeConfiguration())->setId('testTheme');

        foreach ($settingValues as $name => $value) {
            $config->addThemeSetting((new Setting())->setName($name)->setType('str')->setValue($value));
        }

        return $config;
    }

    private function assertSettingValue(mixed $expected, string $name, ThemeConfiguration $config): void
    {
        foreach ($config->getThemeSettings() as $setting) {
            if ($setting->getName() === $name) {
                $this->assertSame($expected, $setting->getValue());
                return;
            }
        }

        $this->fail("Setting '$name' not found in configuration");
    }
}