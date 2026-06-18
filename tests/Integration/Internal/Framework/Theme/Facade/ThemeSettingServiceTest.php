<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Exception\ThemeSettingNotFoundException;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ThemeSettingServiceTest extends IntegrationTestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'testTheme';

    public function testGetStringReturnsSavedValue(): void
    {
        $this->saveThemeWithSetting('sLogoFile', 'str', 'logo.png');

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertSame('logo.png', $service->getString('sLogoFile'));
    }

    public function testGetBooleanReturnsSavedValue(): void
    {
        $this->saveThemeWithSetting('blShowWishlist', 'bool', true);

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertTrue($service->getBoolean('blShowWishlist'));
    }

    public function testGetIntegerReturnsSavedValue(): void
    {
        $this->saveThemeWithSetting('iNewArticles', 'int', 5);

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertSame(5, $service->getInteger('iNewArticles'));
    }

    public function testGetFloatReturnsSavedValue(): void
    {
        $this->saveThemeWithSetting('fSomeFloat', 'num', 1.5);

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertSame(1.5, $service->getFloat('fSomeFloat'));
    }

    public function testGetCollectionReturnsSavedValue(): void
    {
        $this->saveThemeWithSetting('aNrofCatArticles', 'arr', ['10', '20', '50']);

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertSame(['10', '20', '50'], $service->getCollection('aNrofCatArticles'));
    }

    public function testExistsReturnsTrueForExistingSetting(): void
    {
        $this->saveThemeWithSetting('sLogoFile', 'str', 'logo.png');

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertTrue($service->exists('sLogoFile'));
    }

    public function testExistsReturnsFalseForMissingSetting(): void
    {
        $this->saveThemeWithSetting('sLogoFile', 'str', 'logo.png');

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertFalse($service->exists('nonExistent'));
    }

    public function testExistsReturnsFalseWhenNoThemeConfigurationExists(): void
    {
        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertFalse($service->exists('sLogoFile'));
    }

    public function testGetThrowsForMissingSetting(): void
    {
        $this->saveThemeWithSetting('sLogoFile', 'str', 'logo.png');

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->expectException(ThemeSettingNotFoundException::class);
        $service->getString('nonExistent');
    }

    public function testGetThrowsForMissingThemeConfiguration(): void
    {
        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->expectException(ThemeSettingNotFoundException::class);
        $service->getBoolean('bl_showWishlist');
    }

    private function saveThemeWithSetting(string $name, string $type, mixed $value): void
    {
        $setting = (new Setting())->setName($name)->setType($type)->setValue($value);
        $configuration = (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->setActivated(true)
            ->addThemeSetting($setting);

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, self::SHOP_ID);
    }
}
