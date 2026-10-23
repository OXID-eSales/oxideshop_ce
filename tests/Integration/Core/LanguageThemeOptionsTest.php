<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class LanguageThemeOptionsTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;
    private const GERMAN = 0;
    private const THEME_ID = 'optionsTheme';
    private const SECOND_THEME_ID = 'secondOptionsTheme';

    private string $fixtureDirectory = __DIR__ . '/Fixtures/vendorThemeOptions';

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.shop_source_directory', "$this->fixtureDirectory/shop/source/");
        $this->installTheme(self::THEME_ID);
    }

    public function testThemeOptionsTranslationIsReadFromLanguageDirectoryOfInstalledTheme(): void
    {
        $this->assertSame(
            'from language directory',
            $this->translateInAdmin('TEST_THEME_OPTIONS_LANGUAGE_DIRECTORY_KEY')
        );
    }

    public function testThemeOptionsTranslationIsReadFromTranslationsDirectoryOfInstalledTheme(): void
    {
        $this->assertSame(
            'from translations directory',
            $this->translateInAdmin('TEST_THEME_OPTIONS_TRANSLATIONS_DIRECTORY_KEY')
        );
    }

    public function testThemeOptionsTranslationOfNotInstalledThemeInViewsDirectoryIsNotRead(): void
    {
        $this->assertSame(
            'TEST_THEME_OPTIONS_NOT_INSTALLED_KEY',
            $this->translateInAdmin('TEST_THEME_OPTIONS_NOT_INSTALLED_KEY')
        );
    }

    public function testTranslationsDirectoryOverridesLanguageDirectoryOfOtherInstalledThemes(): void
    {
        $this->installTheme(self::SECOND_THEME_ID);

        $this->assertSame(
            'from translations directory of first theme',
            $this->translateInAdmin('TEST_THEME_OPTIONS_ORDER_KEY')
        );
    }

    public function testThemeOptionsTranslationIsReadWhenAnotherThemeConfigurationIsBroken(): void
    {
        $this->saveBrokenConfiguration('brokenOptionsTheme');

        $this->assertSame(
            'from language directory',
            $this->translateInAdmin('TEST_THEME_OPTIONS_LANGUAGE_DIRECTORY_KEY')
        );
    }

    private function saveBrokenConfiguration(string $themeId): void
    {
        (new Filesystem())->dumpFile(
            Path::join(
                $this->get(BasicContextInterface::class)->getShopConfigurationDirectory(self::SHOP_ID),
                'themes',
                "$themeId.yaml"
            ),
            "themeSettings: [unclosed\n"
        );
        Registry::getUtils()->resetLanguageCache();
    }

    private function installTheme(string $themeId): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->getThemeDirectory($themeId));

        Registry::getUtils()->resetLanguageCache();
    }

    private function translateInAdmin(string $key): string
    {
        return (new Language())->translateString($key, self::GERMAN, true);
    }

    private function getThemeDirectory(string $themeId): string
    {
        return realpath("$this->fixtureDirectory/vendor/$themeId");
    }
}
