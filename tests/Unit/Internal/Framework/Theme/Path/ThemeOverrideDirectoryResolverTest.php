<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Theme\Path;

use OxidEsales\EshopCommunity\Internal\Domain\Locale\DataObject\Locale;
use OxidEsales\EshopCommunity\Internal\Domain\Locale\Service\ActiveLocaleProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Path\ThemeOverrideDirectoryResolver;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ThemeOverrideDirectoryResolverTest extends TestCase
{
    private const SHOP_ID = 1;
    private const THEME_ID = 'apex';
    private const LANGUAGE_CODE = 'de';
    private const SOURCE_PATH = '/var/www/source';

    public function testResolveReturnsEmptyArrayWhenNoneExist(): void
    {
        $resolver = $this->createResolver($this->createFilesystemStub(existingDirectories: []));

        $this->assertSame([], $resolver->resolve(self::THEME_ID, self::SHOP_ID));
    }

    public function testResolveReturnsOnlyExistingDirectoriesInPriorityOrder(): void
    {
        $shopLanguageDirectory = $this->themeDirectory('1', self::LANGUAGE_CODE, 'tpl');
        $languageDirectory = $this->themeDirectory(self::LANGUAGE_CODE, 'tpl');

        $resolver = $this->createResolver($this->createFilesystemStub(existingDirectories: [
            $shopLanguageDirectory,
            $languageDirectory,
        ]));

        $this->assertSame(
            [$shopLanguageDirectory, $languageDirectory],
            $resolver->resolve(self::THEME_ID, self::SHOP_ID)
        );
    }

    public function testResolveReturnsAllThreeTiersWhenAllExist(): void
    {
        $shopLanguageDirectory = $this->themeDirectory('1', self::LANGUAGE_CODE, 'tpl');
        $shopDirectory = $this->themeDirectory('1', 'tpl');
        $languageDirectory = $this->themeDirectory(self::LANGUAGE_CODE, 'tpl');

        $resolver = $this->createResolver($this->createFilesystemStub(existingDirectories: [
            $shopLanguageDirectory,
            $shopDirectory,
            $languageDirectory,
        ]));

        $this->assertSame(
            [$shopLanguageDirectory, $shopDirectory, $languageDirectory],
            $resolver->resolve(self::THEME_ID, self::SHOP_ID)
        );
    }

    private function themeDirectory(string ...$segments): string
    {
        return Path::join(self::SOURCE_PATH, 'Application', 'views', self::THEME_ID, ...$segments);
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
        $context = $this->createStub(BasicContextInterface::class);
        $context->method('getSourcePath')->willReturn(self::SOURCE_PATH);

        $activeLocaleProvider = $this->createStub(ActiveLocaleProviderInterface::class);
        $activeLocaleProvider->method('getActiveLocale')->willReturn(
            new Locale(self::LANGUAGE_CODE, 'Deutsch', 'en')
        );

        return new ThemeOverrideDirectoryResolver($context, $activeLocaleProvider, $filesystem);
    }
}
