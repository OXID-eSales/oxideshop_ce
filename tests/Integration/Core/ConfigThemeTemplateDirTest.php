<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\Filesystem\Path;

final class ConfigThemeTemplateDirTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;
    private const GERMAN = 0;
    private const THEME_ID = 'vendorTheme';
    private const CHILD_THEME_ID = 'vendorChildTheme';
    private const ADMIN_THEME_ID = 'vendorAdminTheme';
    private const TEMPLATE_DIR = 'tpl';

    private string $fixtureDirectory = __DIR__ . '/Fixtures/vendorTemplates';

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.theme.active', self::THEME_ID);
        $this->setParameter('oxid_esales.theme.admin.name', self::ADMIN_THEME_ID);
        $this->setParameter('oxid_esales.shop_source_directory', $this->getShopSourceDirectory());

        $this->installTheme(self::THEME_ID);
        $this->installTheme(self::CHILD_THEME_ID);

        Registry::getConfig()->reinitialize();
        Registry::getUtils()->cleanStaticCache();
    }

    public static function templateLevelDataProvider(): array
    {
        return [
            'shop and language level' => ['shop-language.html.twig', '1/de/tpl/shop-language.html.twig'],
            'shop level' => ['shop.html.twig', '1/tpl/shop.html.twig'],
            'language level' => ['language.html.twig', 'de/tpl/language.html.twig'],
            'theme level' => ['theme.html.twig', 'tpl/theme.html.twig'],
        ];
    }

    #[DataProvider('templateLevelDataProvider')]
    public function testTemplateIsReadFromThemeDirectory(string $template, string $pathInTheme): void
    {
        $path = Registry::getConfig()->getDir($template, self::TEMPLATE_DIR, false, self::GERMAN, self::SHOP_ID);

        $this->assertSame(Path::join($this->getThemeDirectory(self::THEME_ID), $pathInTheme), $path);
    }

    public function testTemplateIsReturnedRelativeToShopSourceDirectory(): void
    {
        $path = Registry::getConfig()->getDir(
            'theme.html.twig',
            self::TEMPLATE_DIR,
            false,
            self::GERMAN,
            self::SHOP_ID,
            null,
            false
        );

        $this->assertSame(
            Path::join(
                Path::makeRelative($this->getThemeDirectory(self::THEME_ID), $this->getShopSourceDirectory()),
                'tpl/theme.html.twig'
            ),
            $path
        );
    }

    public function testTemplateOfNotInstalledThemeIsNotFound(): void
    {
        $path = Registry::getConfig()->getDir(
            'theme.html.twig',
            self::TEMPLATE_DIR,
            false,
            self::GERMAN,
            self::SHOP_ID,
            'notInstalledTheme'
        );

        $this->assertFalse($path);
    }

    public function testTemplateFallsBackToParentThemeDirectory(): void
    {
        $this->activateTheme(self::CHILD_THEME_ID);

        $this->assertSame(
            Path::join($this->getThemeDirectory(self::THEME_ID), 'tpl/theme.html.twig'),
            Registry::getConfig()->getTemplatePath('theme.html.twig', false)
        );
    }

    public function testTemplateOfChildThemeIsReadFromChildThemeDirectory(): void
    {
        $this->activateTheme(self::CHILD_THEME_ID);

        $this->assertSame(
            Path::join($this->getThemeDirectory(self::CHILD_THEME_ID), 'tpl/child.html.twig'),
            Registry::getConfig()->getTemplatePath('child.html.twig', false)
        );
    }

    public function testAdminTemplateIsReadFromViewsDirectory(): void
    {
        $this->assertSame(
            Path::join($this->getShopSourceDirectory(), 'Application/views', self::ADMIN_THEME_ID, 'tpl/admin.html.twig'),
            Registry::getConfig()->getTemplatePath('admin.html.twig', true)
        );
    }

    private function activateTheme(string $themeId): void
    {
        $this->setParameter('oxid_esales.theme.active', $themeId);
        Registry::getConfig()->reinitialize();
        Registry::getUtils()->cleanStaticCache();
    }

    private function installTheme(string $themeId): void
    {
        $this->get(ThemeConfigurationInstallerInterface::class)->install($this->getThemeDirectory($themeId));
    }

    private function getThemeDirectory(string $themeId): string
    {
        return realpath("$this->fixtureDirectory/vendor/$themeId");
    }

    private function getShopSourceDirectory(): string
    {
        return realpath("$this->fixtureDirectory/shop/source");
    }
}
