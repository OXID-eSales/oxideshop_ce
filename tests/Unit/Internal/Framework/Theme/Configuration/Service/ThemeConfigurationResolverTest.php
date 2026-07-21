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
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

final class ThemeConfigurationResolverTest extends TestCase
{
    private const THEME_ID = 'testTheme';
    private const SHOP_ID = 1;

    public function testResolveLogsAndIgnoresUnknownEnvironmentSetting(): void
    {
        $configuration = $this->createConfiguration();

        $configurationDao = $this->createStub(ThemeConfigurationDaoInterface::class);
        $configurationDao->method('get')->willReturn($configuration);

        $environmentConfigurationDao = $this->createStub(ThemeEnvironmentConfigurationDaoInterface::class);
        $environmentConfigurationDao
            ->method('get')
            ->willReturn(
                new ThemeEnvironmentConfiguration([
                    'unknownSetting' => 'ignoredValue',
                ])
            );

        $logger = $this->createMock(LoggerInterface::class);
        $logger
            ->expects($this->once())
            ->method('warning')
            ->with(
                'Environment configuration references an unknown theme setting.'
                . ' The environment value will be ignored.',
                [
                    'themeId' => self::THEME_ID,
                    'shopId' => self::SHOP_ID,
                    'settingName' => 'unknownSetting',
                ]
            );

        $resolvedConfiguration = $this->createResolver(
            $configurationDao,
            $environmentConfigurationDao,
            $logger
        )->resolve(self::THEME_ID, self::SHOP_ID);

        $this->assertSame(
            'canonicalValue',
            $resolvedConfiguration->getSettingByName('canonicalSetting')->getValue()
        );
        $this->assertNull($resolvedConfiguration->getSettingByName('unknownSetting'));
    }

    private function createResolver(
        ThemeConfigurationDaoInterface $configurationDao,
        ThemeEnvironmentConfigurationDaoInterface $environmentConfigurationDao,
        LoggerInterface $logger,
    ): ThemeConfigurationResolver {
        return new ThemeConfigurationResolver(
            $configurationDao,
            $environmentConfigurationDao,
            new ThemeConfigurationCache(),
            $logger
        );
    }

    private function createConfiguration(): ThemeConfiguration
    {
        return (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->addThemeSetting(
                (new Setting())
                    ->setName('overriddenSetting')
                    ->setValue('canonicalValue')
            )
            ->addThemeSetting(
                (new Setting())
                    ->setName('canonicalSetting')
                    ->setValue('canonicalValue')
            );
    }
}
