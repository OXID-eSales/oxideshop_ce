<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\InvalidThemeConfigurationException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Exception\ThemeNotLoadableException;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeConfigurationInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Psr\Log\LoggerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ConfigThemeTemplateDirTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const SHOP_ID = 1;
    private const GERMAN = 0;
    private const THEME_ID = 'vendorTheme';
    private const CHILD_THEME_ID = 'vendorChildTheme';
    private const BROKEN_THEME_ID = 'brokenTheme';
    private const TEMPLATE_DIR = 'tpl';

    private string $fixtureDirectory = __DIR__ . '/Fixtures/vendorTemplates';

    public function setUp(): void
    {
        parent::setUp();

        $this->setParameter('oxid_esales.theme.active', self::THEME_ID);
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

    public function testNotLoadableThemeIsLoggedOnce(): void
    {
        $logger = $this->createMock(LoggerInterface::class);
        Registry::set('logger', $logger);

        $logger->expects($this->once())
            ->method('warning')
            ->with($this->anything(), $this->containsOnlyInstancesOf(ThemeNotLoadableException::class));

        $this->assertFalse($this->getTemplateOfNotInstalledTheme());
        $this->assertFalse($this->getTemplateOfNotInstalledTheme());
    }

    public function testActiveThemeWithBrokenConfigurationIsLogged(): void
    {
        $this->saveBrokenConfiguration(self::BROKEN_THEME_ID);
        $this->activateTheme(self::BROKEN_THEME_ID);
        $logger = $this->createMock(LoggerInterface::class);
        Registry::set('logger', $logger);

        $logger->expects($this->once())
            ->method('warning')
            ->with($this->anything(), $this->containsOnlyInstancesOf(InvalidThemeConfigurationException::class));

        $this->assertFalse(Registry::getConfig()->getTemplatePath('theme.html.twig', false));
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

    private function activateTheme(string $themeId): void
    {
        $this->setParameter('oxid_esales.theme.active', $themeId);
        Registry::getConfig()->reinitialize();
        Registry::getUtils()->cleanStaticCache();
    }

    private function getTemplateOfNotInstalledTheme(): string|false
    {
        return Registry::getConfig()->getDir(
            'theme.html.twig',
            self::TEMPLATE_DIR,
            false,
            self::GERMAN,
            self::SHOP_ID,
            'notInstalledTheme'
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
        return Path::join(realpath($this->fixtureDirectory), 'shop/source');
    }
}
