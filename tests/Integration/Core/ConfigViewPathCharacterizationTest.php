<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\AdminThemeBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\DataObject\ThemeConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Facade\ActiveThemeServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Setup\Bridge\ThemeActivationBridgeInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

/**
 * Pins the CURRENT behaviour of the view-path/url methods on Core\Config so the planned
 * extraction of these responsibilities into dedicated services is provably behaviour-preserving.
 *
 * A dedicated theme is installed + activated (the integration test container has no active theme
 * by default), with its out/ asset dirs created, so getDir() resolves deterministically. Assertions
 * anchor on getOutDir()/getOutUrl()/ActiveThemeService rather than hardcoded paths.
 */
final class ConfigViewPathCharacterizationTest extends IntegrationTestCase
{
    private const THEME_ID = 'oe_charpin_test_theme';
    private const THEME_SOURCE = 'tests/Integration/Core/Fixtures/oe_charpin_test_theme';

    /** @var string[] */
    private array $createdPaths = [];

    public function setUp(): void
    {
        parent::setUp();

        $shopId = (int) $this->config()->getShopId();
        ContainerFacade::get(ThemeConfigurationDaoInterface::class)->save(
            (new ThemeConfiguration())->setId(self::THEME_ID)->setThemeSource(self::THEME_SOURCE),
            $shopId
        );
        ContainerFacade::get(ThemeActivationBridgeInterface::class)->activate(self::THEME_ID, $shopId);

        $this->makeOutDir(self::THEME_ID . '/src');
        $this->makeOutDir(self::THEME_ID . '/img');
    }

    public function tearDown(): void
    {
        $shopId = (int) $this->config()->getShopId();
        ContainerFacade::get(ThemeActivationBridgeInterface::class)->deactivate(self::THEME_ID, $shopId);
        ContainerFacade::get(ThemeConfigurationDaoInterface::class)->delete(self::THEME_ID, $shopId);

        foreach (array_reverse($this->createdPaths) as $path) {
            if (is_file($path)) {
                unlink($path);
            }
        }
        foreach (array_reverse($this->createdPaths) as $path) {
            if (is_dir($path) && !(new \FilesystemIterator($path))->valid()) {
                rmdir($path);
            }
        }
        (new Filesystem())->remove($this->config()->getOutDir() . self::THEME_ID);

        parent::tearDown();
    }

    private function config(): Config
    {
        return Registry::getConfig();
    }

    public function testResourcePathIsAnchoredOnOutDirAndActiveTheme(): void
    {
        $config = $this->config();

        $this->assertSame(
            $config->getOutDir() . self::THEME_ID . '/src/',
            $config->getResourcePath('', false)
        );
    }

    public function testImageDirIsAnchoredOnOutDirAndActiveTheme(): void
    {
        $config = $this->config();

        $this->assertSame(
            $config->getOutDir() . self::THEME_ID . '/img/',
            $config->getImageDir(false)
        );
    }

    public function testPictureDirsAreAnchoredOnOutDir(): void
    {
        $config = $this->config();

        $this->assertSame($config->getOutDir() . 'pictures/', $config->getPictureDir(false));
        $this->assertSame($config->getOutDir() . 'pictures/master/', $config->getMasterPictureDir(false));
    }

    public function testResourceUrlIsResourcePathWithOutUrlSubstituted(): void
    {
        $config = $this->config();
        $nativeImg = $config->getConfigParam('blNativeImages');

        $this->assertSame(
            str_replace($config->getOutDir(), $config->getOutUrl(null, false, $nativeImg), $config->getResourcePath('', false)),
            $config->getResourceUrl('', false)
        );
    }

    public function testTemplateDirResolvesToActiveThemeVendorSource(): void
    {
        $config = $this->config();
        $activeSource = ContainerFacade::get(ActiveThemeServiceInterface::class)
            ->getActiveThemeSourcePaths()[self::THEME_ID];

        $this->assertSame(
            Path::join($activeSource, 'tpl') . DIRECTORY_SEPARATOR,
            $config->getTemplateDir(false)
        );
    }

    public function testGetDirReturnsFalseForMissingFile(): void
    {
        $this->assertFalse($this->config()->getDir('this_file_does_not_exist_xyz.css', 'src', false));
    }

    public function testPictureUrlFallsBackToNopicForMissingFile(): void
    {
        $url = $this->config()->getPictureUrl('generated/does_not_exist_xyz.jpg', false);

        $this->assertStringEndsWith('pictures/master/nopic.jpg', $url);
    }

    public function testAdminResourcePathDiffersFromFrontend(): void
    {
        $config = $this->config();
        $adminTheme = ContainerFacade::get(AdminThemeBridgeInterface::class)->getActiveTheme();

        $admin = (string) $config->getResourcePath('', true);

        $this->assertNotSame($config->getResourcePath('', false), $admin);
        $this->assertStringContainsString($adminTheme . '/src/', $admin);
    }

    public function testGetDirFallbackPrefersThemeLevelOverOutLevel(): void
    {
        $config = $this->config();
        $marker = 'charpin_theme_wins.txt';

        $this->writeOutFile(self::THEME_ID . "/src/$marker");
        $this->writeOutFile("src/$marker");

        $this->assertSame(
            $config->getOutDir() . self::THEME_ID . "/src/$marker",
            $config->getResourcePath($marker, false)
        );
    }

    public function testGetDirFallsBackToOutLevelWhenThemeLevelAbsent(): void
    {
        $config = $this->config();
        $marker = 'charpin_out_only.txt';

        $this->writeOutFile("src/$marker");

        $this->assertSame(
            $config->getOutDir() . "src/$marker",
            $config->getResourcePath($marker, false)
        );
    }

    private function makeOutDir(string $relativePath): void
    {
        $absolute = $this->config()->getOutDir() . $relativePath;
        if (!is_dir($absolute)) {
            mkdir($absolute, 0o775, true);
            $this->createdPaths[] = $absolute;
        }
    }

    private function writeOutFile(string $relativePath): void
    {
        $absolute = $this->config()->getOutDir() . $relativePath;
        $dir = \dirname($absolute);
        if (!is_dir($dir)) {
            mkdir($dir, 0o775, true);
            $this->createdPaths[] = $dir;
        }
        file_put_contents($absolute, 'characterization');
        $this->createdPaths[] = $absolute;
    }
}
