<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\FileSystem\ConfiguredShopIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Provider\ThemeConfigurationProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstaller;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationMergerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class ThemeConfigurationInstallerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $themePath;
    private string $themeId = 'testTheme';

    public function setUp(): void
    {
        parent::setUp();

        $this->themePath = realpath(__DIR__ . '/../Fixtures/testTheme');
    }

    public function testInstallCreatesYamlForAllShops(): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->themePath);

        foreach ($this->get(BasicContextInterface::class)->getAllShopIds() as $shopId) {
            $this->assertTrue(
                $this->get(ThemeConfigurationDaoInterface::class)->exists($this->themeId, $shopId)
            );
        }
    }

    public function testInstallCreatesYamlForConfiguredSubshops(): void
    {
        $subshopDirectory = $this->get(BasicContextInterface::class)->getShopConfigurationDirectory(2);
        $this->get('oxid_esales.symfony.file_system')->mkdir($subshopDirectory);

        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->themePath);

        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $this->assertTrue($dao->exists($this->themeId, 1));
        $this->assertTrue($dao->exists($this->themeId, 2));
    }

    public function testInstallSetsActivatedToFalseByDefault(): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->themePath);

        $configuration = $this->get(ThemeConfigurationDaoInterface::class)
            ->get($this->themeId, $this->get(BasicContextInterface::class)->getDefaultShopId());

        $this->assertFalse($configuration->isActivated());
    }

    public function testInstallWritesDefaultSettingValues(): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->themePath);

        $configuration = $this->get(ThemeConfigurationDaoInterface::class)
            ->get($this->themeId, $this->get(BasicContextInterface::class)->getDefaultShopId());

        $settings = [];
        foreach ($configuration->getThemeSettings() as $setting) {
            $settings[$setting->getName()] = $setting->getValue();
        }

        $this->assertSame('defaultValue', $settings['testStringSetting']);
        $this->assertTrue($settings['testBoolSetting']);
        $this->assertSame('option1', $settings['testSelectSetting']);
    }

    public function testReinstallPreservesCustomisedSettingValues(): void
    {
        $installer = $this->get(ThemeConfigurationInstallerInterface::class);
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $shopId = $this->get(BasicContextInterface::class)->getDefaultShopId();

        $installer->install($this->themePath);

        $configuration = $dao->get($this->themeId, $shopId);
        foreach ($configuration->getThemeSettings() as $setting) {
            if ($setting->getName() === 'testStringSetting') {
                $setting->setValue('customValue');
            }
        }
        $dao->save($configuration, $shopId);

        $installer->install($this->themePath);

        $reinstalled = $dao->get($this->themeId, $shopId);
        foreach ($reinstalled->getThemeSettings() as $setting) {
            if ($setting->getName() === 'testStringSetting') {
                $this->assertSame('customValue', $setting->getValue());
                return;
            }
        }

        $this->fail('Setting testStringSetting not found after reinstall');
    }

    public function testInstallSavesThemeTitle(): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->themePath);

        $configuration = $this->get(ThemeConfigurationDaoInterface::class)
            ->get($this->themeId, $this->get(BasicContextInterface::class)->getDefaultShopId());

        $this->assertSame('Test Theme', $configuration->getTitle());
    }

    public function testInstallSavesSourcePath(): void
    {
        $context = $this->get(BasicContextInterface::class);
        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->themePath);

        $configuration = $this->get(ThemeConfigurationDaoInterface::class)
            ->get($this->themeId, $context->getDefaultShopId());

        $expectedSource = Path::makeRelative($this->themePath, $context->getShopRootPath());
        $this->assertSame($expectedSource, $configuration->getSource());
    }

    public function testIsInstalledReturnsFalseBeforeInstall(): void
    {
        $this->assertFalse(
            $this->get(ThemeConfigurationInstallerInterface::class)->isInstalled($this->themePath)
        );
    }

    public function testIsInstalledReturnsTrueAfterInstall(): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->themePath);

        $this->assertTrue(
            $this->get(ThemeConfigurationInstallerInterface::class)->isInstalled($this->themePath)
        );
    }

    public function testReinstallDoesNotLeakStateAcrossShops(): void
    {
        $dao = $this->get(ThemeConfigurationDaoInterface::class);

        $shopIdProvider = $this->createStub(ConfiguredShopIdProviderInterface::class);
        $shopIdProvider->method('getShopIds')->willReturn([1, 2]);

        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getDefaultShopId')->willReturn(1);
        $context->method('getShopRootPath')->willReturn(
            $this->get(BasicContextInterface::class)->getShopRootPath()
        );

        $installer = new ThemeConfigurationInstaller(
            $this->get(ThemeMetaDataProviderInterface::class),
            $this->get(ThemeConfigurationProviderInterface::class),
            $dao,
            $this->get(ThemeConfigurationMergerInterface::class),
            $shopIdProvider,
            $context,
        );

        $installer->install($this->themePath);

        $shop1Config = $dao->get($this->themeId, 1);
        $shop1Config->setActivated(true);
        foreach ($shop1Config->getThemeSettings() as $setting) {
            if ($setting->getName() === 'testStringSetting') {
                $setting->setValue('shop1custom');
            }
        }
        $dao->save($shop1Config, 1);

        $installer->install($this->themePath);

        $reinstalledShop2 = $dao->get($this->themeId, 2);

        $this->assertFalse($reinstalledShop2->isActivated(), 'Shop 2 must not inherit shop 1 activated state');

        $settings = [];
        foreach ($reinstalledShop2->getThemeSettings() as $setting) {
            $settings[$setting->getName()] = $setting->getValue();
        }
        $this->assertSame('defaultValue', $settings['testStringSetting'], 'Shop 2 must not inherit shop 1 custom value');
    }
}
