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
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class LanguageThemeOptionsTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const GERMAN = 0;
    private const THEME_ID = 'optionsTheme';

    private string $fixtureDirectory = __DIR__ . '/Fixtures/vendorThemeOptions';

    public function setUp(): void
    {
        parent::setUp();

        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->getThemeDirectory(self::THEME_ID));

        Registry::getUtils()->resetLanguageCache();
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

    public function testThemeOptionsTranslationOfNotInstalledThemeIsNotRead(): void
    {
        $this->assertSame(
            'TEST_THEME_OPTIONS_NOT_INSTALLED_KEY',
            $this->translateInAdmin('TEST_THEME_OPTIONS_NOT_INSTALLED_KEY')
        );
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
