<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Cache\ThemeConfigurationCache;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeEnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaData;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\ThemeMetaDataByIdProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ThemeConfigurationResolverTest extends TestCase
{
    private const SHOP_ID = 1;
    private const CHILD_THEME_ID = 'childTheme';
    private const PARENT_THEME_ID = 'parentTheme';

    public function testResolveAppliesEnvironmentOverrideToOwnSetting(): void
    {
        $configuration = $this->createConfiguration(self::CHILD_THEME_ID, ['logoFile' => 'default.png']);

        $resolvedConfiguration = $this
            ->createResolver(
                configurations: [$configuration],
                environmentValues: ['logoFile' => 'environment.png']
            )
            ->resolve(self::CHILD_THEME_ID, self::SHOP_ID);

        $this->assertSame('environment.png', $resolvedConfiguration->getSettingByName('logoFile')->getValue());
    }

    public function testResolveLogsAndIgnoresUnknownEnvironmentSetting(): void
    {
        $configuration = $this->createConfiguration(self::CHILD_THEME_ID, ['logoFile' => 'default.png']);

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('warning')
            ->with(
                'Environment configuration references an unknown theme setting.'
                . ' The environment value will be ignored.',
                [
                    'themeId' => self::CHILD_THEME_ID,
                    'shopId' => self::SHOP_ID,
                    'settingName' => 'unknownSetting',
                ]
            );

        $resolvedConfiguration = $this
            ->createResolver(
                configurations: [$configuration],
                environmentValues: ['unknownSetting' => 'ignoredValue'],
                logger: $logger
            )
            ->resolve(self::CHILD_THEME_ID, self::SHOP_ID);

        $this->assertSame('default.png', $resolvedConfiguration->getSettingByName('logoFile')->getValue());
        $this->assertNull($resolvedConfiguration->getSettingByName('unknownSetting'));
    }

    public function testResolveMergesParentSettingChildDoesNotDeclare(): void
    {
        $resolvedConfiguration = $this
            ->createResolver(
                configurations: [
                    $this->createConfiguration(self::CHILD_THEME_ID, []),
                    $this->createConfiguration(self::PARENT_THEME_ID, ['iconSize' => '87*87']),
                ],
                parentThemeId: self::PARENT_THEME_ID
            )
            ->resolve(self::CHILD_THEME_ID, self::SHOP_ID);

        $this->assertSame('87*87', $resolvedConfiguration->getSettingByName('iconSize')->getValue());
    }

    public function testResolveKeepsChildValueForSettingDeclaredByBothThemes(): void
    {
        $resolvedConfiguration = $this
            ->createResolver(
                configurations: [
                    $this->createConfiguration(self::CHILD_THEME_ID, ['iconSize' => '100*100']),
                    $this->createConfiguration(self::PARENT_THEME_ID, ['iconSize' => '87*87']),
                ],
                parentThemeId: self::PARENT_THEME_ID
            )
            ->resolve(self::CHILD_THEME_ID, self::SHOP_ID);

        $this->assertSame('100*100', $resolvedConfiguration->getSettingByName('iconSize')->getValue());
    }

    public function testResolveAppliesEnvironmentOverrideToInheritedSetting(): void
    {
        $resolvedConfiguration = $this
            ->createResolver(
                configurations: [
                    $this->createConfiguration(self::CHILD_THEME_ID, []),
                    $this->createConfiguration(self::PARENT_THEME_ID, ['iconSize' => '87*87']),
                ],
                environmentValues: ['iconSize' => '200*200'],
                parentThemeId: self::PARENT_THEME_ID
            )
            ->resolve(self::CHILD_THEME_ID, self::SHOP_ID);

        $this->assertSame('200*200', $resolvedConfiguration->getSettingByName('iconSize')->getValue());
    }

    public function testResolveInheritedValueDoesNotChangeParentConfiguration(): void
    {
        $parentConfiguration = $this->createConfiguration(self::PARENT_THEME_ID, ['iconSize' => '87*87']);

        $this
            ->createResolver(
                configurations: [
                    $this->createConfiguration(self::CHILD_THEME_ID, []),
                    $parentConfiguration,
                ],
                environmentValues: ['iconSize' => '200*200'],
                parentThemeId: self::PARENT_THEME_ID
            )
            ->resolve(self::CHILD_THEME_ID, self::SHOP_ID);

        $this->assertSame('87*87', $parentConfiguration->getSettingByName('iconSize')->getValue());
    }

    public function testResolveReturnsOwnSettingsWhenParentConfigurationIsMissing(): void
    {
        $resolvedConfiguration = $this
            ->createResolver(
                configurations: [$this->createConfiguration(self::CHILD_THEME_ID, ['logoFile' => 'child.png'])],
                parentThemeId: self::PARENT_THEME_ID
            )
            ->resolve(self::CHILD_THEME_ID, self::SHOP_ID);

        $this->assertSame('child.png', $resolvedConfiguration->getSettingByName('logoFile')->getValue());
        $this->assertNull($resolvedConfiguration->getSettingByName('iconSize'));
    }

    /** @param ThemeConfiguration[] $configurations */
    private function createResolver(
        array $configurations,
        array $environmentValues = [],
        string $parentThemeId = '',
        ?LoggerInterface $logger = null
    ): ThemeConfigurationResolver {
        $configurationsById = [];
        foreach ($configurations as $configuration) {
            $configurationsById[$configuration->getId()] = $configuration;
        }

        $configurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $configurationDao
            ->method('get')
            ->willReturnCallback(fn (string $themeId) => clone $configurationsById[$themeId]);
        $configurationDao
            ->method('exists')
            ->willReturnCallback(fn (string $themeId) => isset($configurationsById[$themeId]));

        $environmentConfigurationDao = $this->createStub(ThemeEnvironmentConfigurationDaoInterface::class);
        $environmentConfigurationDao
            ->method('get')
            ->willReturn(new ThemeEnvironmentConfiguration($environmentValues));

        $metaDataProvider = $this->createStub(ThemeMetaDataByIdProviderInterface::class);
        $metaDataProvider->method('getById')->willReturn(
            (new ThemeMetaData())->setId(self::CHILD_THEME_ID)->setParentTheme($parentThemeId)
        );

        return new ThemeConfigurationResolver(
            $configurationDao,
            $environmentConfigurationDao,
            new ThemeConfigurationCache(),
            $metaDataProvider,
            $logger ?? $this->createStub(LoggerInterface::class)
        );
    }

    private function createConfiguration(string $themeId, array $settingValues): ThemeConfiguration
    {
        $configuration = (new ThemeConfiguration())->setId($themeId);

        foreach ($settingValues as $name => $value) {
            $configuration->addThemeSetting((new Setting())->setName($name)->setValue($value));
        }

        return $configuration;
    }
}
