<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Configuration\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeConfigurationNotFoundException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Service\ThemeConfigurationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\State\Exception\ActiveThemeNotFoundException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemeConfigurationServiceTest extends IntegrationTestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'testTheme';

    public function testGetConfigurationReturnsSavedConfiguration(): void
    {
        $this->saveThemeConfiguration(activated: false);

        $configuration = $this->get(ThemeConfigurationServiceInterface::class)->getConfiguration(self::THEME_ID);

        $this->assertSame(self::THEME_ID, $configuration->getId());
        $this->assertSame('100*100', $configuration->getSettingByName('sIconSize')->getValue());
    }

    public function testGetConfigurationThrowsForUnknownTheme(): void
    {
        $this->expectException(ThemeConfigurationNotFoundException::class);

        $this->get(ThemeConfigurationServiceInterface::class)->getConfiguration('unknownTheme');
    }

    public function testGetActiveConfigurationReturnsConfigurationOfActivatedTheme(): void
    {
        $this->saveThemeConfiguration(activated: true);

        $configuration = $this->get(ThemeConfigurationServiceInterface::class)->getActiveConfiguration();

        $this->assertSame(self::THEME_ID, $configuration->getId());
    }

    public function testGetActiveConfigurationThrowsWhenNoThemeIsActivated(): void
    {
        $this->saveThemeConfiguration(activated: false);

        $this->expectException(ActiveThemeNotFoundException::class);

        $this->get(ThemeConfigurationServiceInterface::class)->getActiveConfiguration();
    }

    public function testUpdateSettingsPersistsChangedValues(): void
    {
        $this->saveThemeConfiguration(activated: false);
        $service = $this->get(ThemeConfigurationServiceInterface::class);

        $service->updateSettings($service->getConfiguration(self::THEME_ID), ['sIconSize' => '200*200']);

        $this->assertSame(
            '200*200',
            $service->getConfiguration(self::THEME_ID)->getSettingByName('sIconSize')->getValue()
        );
    }

    private function saveThemeConfiguration(bool $activated): void
    {
        $configuration = (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->setSource('testSourcePath')
            ->setActivated($activated)
            ->addThemeSetting((new Setting())->setName('sIconSize')->setType('str')->setValue('100*100'));

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, self::SHOP_ID);
    }
}
