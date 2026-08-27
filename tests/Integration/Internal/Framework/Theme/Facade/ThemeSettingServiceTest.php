<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Env\EnvUrlFormatter;
use OxidEsales\EshopCommunity\Internal\Framework\Storage\FileStorageFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ThemeSettingServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Setting;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setting\Exception\ThemeSettingNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Path;

final class ThemeSettingServiceTest extends IntegrationTestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'testTheme';

    public function testGetStringReturnsSavedValue(): void
    {
        $this->saveThemeWithSetting('logoFile', 'str', 'logo.png');

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertSame('logo.png', $service->getString('logoFile'));
    }

    public function testGetStringReturnsEnvironmentValueWithoutChangingCanonicalConfiguration(): void
    {
        $this->saveThemeWithSetting('sLogoFile', 'str', 'logo.png');
        $this->saveEnvironmentConfiguration([
            'themeSettings' => [
                'sLogoFile' => ['value' => 'environment-logo.png'],
            ],
        ]);

        $this->assertSame(
            'environment-logo.png',
            $this->get(ThemeSettingServiceInterface::class)->getString('sLogoFile')
        );
        $this->assertSame(
            'logo.png',
            $this->get(ThemeConfigurationDaoInterface::class)
                ->get(self::THEME_ID, self::SHOP_ID)
                ->getSettingByName('sLogoFile')
                ->getValue()
        );
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
        $this->saveThemeWithSetting('numberOfCategoryProducts', 'arr', ['10', '20', '50']);

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertSame(['10', '20', '50'], $service->getCollection('numberOfCategoryProducts'));
    }

    public function testExistsReturnsTrueForExistingSetting(): void
    {
        $this->saveThemeWithSetting('logoFile', 'str', 'logo.png');

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertTrue($service->exists('logoFile'));
    }

    public function testExistsReturnsFalseForMissingSetting(): void
    {
        $this->saveThemeWithSetting('logoFile', 'str', 'logo.png');

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertFalse($service->exists('nonExistent'));
    }

    public function testExistsReturnsFalseWhenNoThemeConfigurationExists(): void
    {
        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->assertFalse($service->exists('logoFile'));
    }

    public function testGetThrowsForMissingSetting(): void
    {
        $this->saveThemeWithSetting('logoFile', 'str', 'logo.png');

        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->expectException(ThemeSettingNotFoundException::class);
        $service->getString('nonExistent');
    }

    public function testGetThrowsForMissingThemeConfiguration(): void
    {
        $service = $this->get(ThemeSettingServiceInterface::class);

        $this->expectException(ThemeSettingNotFoundException::class);
        $service->getBoolean('showWishlist');
    }

    private function saveThemeWithSetting(string $name, string $type, mixed $value): void
    {
        $setting = (new Setting())->setName($name)->setType($type)->setValue($value);
        $configuration = (new ThemeConfiguration())
            ->setId(self::THEME_ID)
            ->setSource(Path::makeRelative(
                __DIR__ . '/Fixtures/testTheme',
                $this->get(BasicContextInterface::class)->getShopRootPath()
            ))
            ->setActivated(true)
            ->addThemeSetting($setting);

        $this->get(ThemeConfigurationDaoInterface::class)->save($configuration, self::SHOP_ID);
    }

    private function saveEnvironmentConfiguration(array $configuration): void
    {
        $path = Path::join(
            EnvUrlFormatter::toEnvUrl(
                $this->get(BasicContextInterface::class)->getProjectConfigurationDirectory()
            ),
            'shops',
            (string) self::SHOP_ID,
            'themes',
            self::THEME_ID . '.yaml'
        );

        $this->get(FileStorageFactoryInterface::class)->create($path)->save($configuration);
    }
}
