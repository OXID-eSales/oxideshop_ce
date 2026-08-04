<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Path;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemeOverrideDirectoryResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;

final class ThemeOverrideDirectoryResolverTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'apex';
    private const LANGUAGE_CODE = 'de';
    private const VIEWS_DIR = '/var/www/Application/views/';

    public function testGetOverrideDirectoriesReturnsEmptyArrayWhenNoneExist(): void
    {
        $resolver = $this->createResolver($this->createFilesystemStub(existingDirectories: []));

        $this->assertSame([], $resolver->getOverrideDirectories(self::THEME_ID, self::SHOP_ID));
    }

    public function testGetOverrideDirectoriesReturnsOnlyExistingDirectoriesInPriorityOrder(): void
    {
        $shopLanguageDirectory = self::VIEWS_DIR . self::THEME_ID . '/1/de/tpl';
        $languageDirectory = self::VIEWS_DIR . self::THEME_ID . '/de/tpl';

        $resolver = $this->createResolver($this->createFilesystemStub(existingDirectories: [
            $shopLanguageDirectory,
            $languageDirectory,
        ]));

        $this->assertSame(
            [$shopLanguageDirectory, $languageDirectory],
            $resolver->getOverrideDirectories(self::THEME_ID, self::SHOP_ID)
        );
    }

    public function testGetOverrideDirectoriesReturnsAllThreeTiersWhenAllExist(): void
    {
        $shopLanguageDirectory = self::VIEWS_DIR . self::THEME_ID . '/1/de/tpl';
        $shopDirectory = self::VIEWS_DIR . self::THEME_ID . '/1/tpl';
        $languageDirectory = self::VIEWS_DIR . self::THEME_ID . '/de/tpl';

        $resolver = $this->createResolver($this->createFilesystemStub(existingDirectories: [
            $shopLanguageDirectory,
            $shopDirectory,
            $languageDirectory,
        ]));

        $this->assertSame(
            [$shopLanguageDirectory, $shopDirectory, $languageDirectory],
            $resolver->getOverrideDirectories(self::THEME_ID, self::SHOP_ID)
        );
    }

    private function createFilesystemStub(array $existingDirectories): Filesystem
    {
        $filesystem = $this->createStub(Filesystem::class);
        $filesystem->method('exists')->willReturnCallback(
            fn(string $directory): bool => in_array($directory, $existingDirectories, true)
        );

        return $filesystem;
    }

    private function createResolver(Filesystem $filesystem): ThemeOverrideDirectoryResolver
    {
        $config = $this->createStub(Config::class);
        $config->method('getViewsDir')->willReturn(self::VIEWS_DIR);

        $activeLocaleProvider = $this->createStub(ActiveLocaleProviderInterface::class);
        $activeLocaleProvider->method('getActiveLocale')->willReturn(
            new Locale(self::LANGUAGE_CODE, 'Deutsch', 'en')
        );

        return new ThemeOverrideDirectoryResolver($config, $activeLocaleProvider, $filesystem);
    }
}
