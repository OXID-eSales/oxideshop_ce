<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Service\ThemeActivationServiceInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class LanguageParentThemeFallbackTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;
    private const PARENT_THEME_ID = 'parentTheme';
    private const CHILD_THEME_ID = 'childTheme';

    private string $fixtureDirectory = __DIR__ . '/Fixtures';

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.shop_source_directory', "$this->fixtureDirectory/shop/source/");

        $this->installTheme(self::PARENT_THEME_ID);
        $this->installTheme(self::CHILD_THEME_ID);
        $this->get(ThemeActivationServiceInterface::class)->activate(self::CHILD_THEME_ID, self::SHOP_ID);
    }

    public function testChildThemeInheritsLanguageFileFromParentTheme(): void
    {
        $translation = (new Language())->translateString('TEST_PARENT_THEME_ONLY_KEY', 0);

        $this->assertSame('from parent theme', $translation);
    }

    public function testChildThemeOverridesSharedTranslationKeyFromParentTheme(): void
    {
        $translation = (new Language())->translateString('TEST_SHARED_KEY', 0);

        $this->assertSame('child value', $translation);
    }

    public function testChildThemeInheritsCustomLanguageFileFromParentTheme(): void
    {
        $translation = (new Language())->translateString('TEST_PARENT_THEME_ONLY_CUSTOM_KEY', 0);

        $this->assertSame('from parent theme custom lang', $translation);
    }

    public function testChildThemeOverridesSharedCustomTranslationKeyFromParentTheme(): void
    {
        $translation = (new Language())->translateString('TEST_SHARED_CUSTOM_KEY', 0);

        $this->assertSame('child custom value', $translation);
    }

    public function testChildThemeInheritsLanguageMapFileFromParentTheme(): void
    {
        $translation = (new Language())->translateString('TEST_PARENT_THEME_MAP_ALIAS_KEY', 0);

        $this->assertSame('from parent theme', $translation);
    }

    public function testLanguageCacheKeyForChildThemeDiffersFromCacheKeyForStandaloneParentTheme(): void
    {
        $childThemeCacheKey = $this->getLangFileCacheName();

        $this->get(ThemeActivationServiceInterface::class)->activate(self::PARENT_THEME_ID, self::SHOP_ID);
        $standaloneParentThemeCacheKey = $this->getLangFileCacheName();

        $this->assertStringEndsWith('_' . self::CHILD_THEME_ID . '_' . self::PARENT_THEME_ID . '_default', $childThemeCacheKey);
        $this->assertStringEndsWith('_' . self::PARENT_THEME_ID . '__default', $standaloneParentThemeCacheKey);
    }

    public function testTranslationValueChangesAfterActivatingDifferentTheme(): void
    {
        $childThemeTranslation = (new Language())->translateString('TEST_SHARED_KEY', 0);

        $this->get(ThemeActivationServiceInterface::class)->activate(self::PARENT_THEME_ID, self::SHOP_ID);
        $standaloneParentThemeTranslation = (new Language())->translateString('TEST_SHARED_KEY', 0);

        $this->assertSame('child value', $childThemeTranslation);
        $this->assertSame('parent value', $standaloneParentThemeTranslation);
    }

    private function getLangFileCacheName(): string
    {
        $method = new \ReflectionMethod(Language::class, 'getLangFileCacheName');

        return $method->invoke(new Language(), false, 0);
    }

    private function installTheme(string $themeId): void
    {
        $themePath = realpath("$this->fixtureDirectory/shop/source/Application/views/$themeId");

        $this->get(ThemeConfigurationInstallerInterface::class)->install($themePath);
    }
}
