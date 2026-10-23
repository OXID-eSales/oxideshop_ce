<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeAssetsInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\MetaData\Exception\InvalidThemeMetaDataException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ThemeAssetsInstallerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const THEME_ID = 'assetsTestTheme';
    private const THEME_WITHOUT_ASSETS_ID = 'testTheme';

    private string $themePath = __DIR__ . '/../Fixtures/assetsTheme';
    private string $themeWithoutAssetsPath = __DIR__ . '/../Fixtures/testTheme';
    private string $brokenMetaDataThemePath = __DIR__ . '/../Fixtures/brokenMetaDataTheme';

    public function tearDown(): void
    {
        (new Filesystem())->remove([
            $this->getAssetsLinkPath(),
            $this->getAssetsLinkPath(self::THEME_WITHOUT_ASSETS_ID),
        ]);

        parent::tearDown();
    }

    public function testThemeAssetsAreNotInstalledByDefault(): void
    {
        $this->assertFalse($this->getAssetsInstaller()->isInstalled($this->themePath));
    }

    public function testThemeAssetsAreInstalledAfterInstall(): void
    {
        $installer = $this->getAssetsInstaller();

        $installer->install($this->themePath);

        $this->assertTrue($installer->isInstalled($this->themePath));
    }

    public function testThemeAssetsAreLinkedToOutDirectoryAfterInstall(): void
    {
        $this->getAssetsInstaller()->install($this->themePath);

        $this->assertFileEquals(
            Path::join($this->themePath, 'assets/some.css'),
            Path::join($this->getAssetsLinkPath(), 'some.css')
        );
    }

    public function testInstallCreatesRelativeSymlink(): void
    {
        $this->getAssetsInstaller()->install($this->themePath);

        $this->assertTrue(Path::isRelative(readlink($this->getAssetsLinkPath())));
    }

    public function testInstallReplacesCopiedAssetsDirectory(): void
    {
        $filesystem = new Filesystem();
        $filesystem->dumpFile(Path::join($this->getAssetsLinkPath(), 'copied.css'), 'body {}');
        $installer = $this->getAssetsInstaller();

        $installer->install($this->themePath);

        $this->assertTrue($installer->isInstalled($this->themePath));
        $this->assertFileDoesNotExist(Path::join($this->getAssetsLinkPath(), 'copied.css'));
    }

    public function testInstallIsRepeatable(): void
    {
        $installer = $this->getAssetsInstaller();
        $installer->install($this->themePath);

        $installer->install($this->themePath);

        $this->assertTrue($installer->isInstalled($this->themePath));
    }

    public function testThemeAssetsAreNotInstalledAfterUninstall(): void
    {
        $installer = $this->getAssetsInstaller();
        $installer->install($this->themePath);

        $installer->uninstall($this->themePath);

        $this->assertFalse($installer->isInstalled($this->themePath));
        $this->assertFileExists(Path::join($this->themePath, 'assets/some.css'));
    }

    public function testInstallSkipsThemeWithoutAssets(): void
    {
        $installer = $this->getAssetsInstaller();

        $installer->install($this->themeWithoutAssetsPath);

        $this->assertFalse(is_link($this->getAssetsLinkPath(self::THEME_WITHOUT_ASSETS_ID)));
        $this->assertFalse($installer->isInstalled($this->themeWithoutAssetsPath));
    }

    public function testBrokenAssetsLinkIsNotInstalled(): void
    {
        (new Filesystem())->symlink('not-existing-directory', $this->getAssetsLinkPath());

        $this->assertFalse($this->getAssetsInstaller()->isInstalled($this->themePath));
    }

    public function testInstallThrowsForThemeWithBrokenMetaData(): void
    {
        $this->expectException(InvalidThemeMetaDataException::class);

        $this->getAssetsInstaller()->install($this->brokenMetaDataThemePath);
    }

    public function testUninstallRemovesCopiedAssetsDirectory(): void
    {
        (new Filesystem())->dumpFile(Path::join($this->getAssetsLinkPath(), 'copied.css'), 'body {}');

        $this->getAssetsInstaller()->uninstall($this->themePath);

        $this->assertDirectoryDoesNotExist($this->getAssetsLinkPath());
    }

    private function getAssetsInstaller(): ThemeAssetsInstallerInterface
    {
        return $this->get(ThemeAssetsInstallerInterface::class);
    }

    private function getAssetsLinkPath(string $themeId = self::THEME_ID): string
    {
        return Path::join($this->get(BasicContextInterface::class)->getOutPath(), $themeId);
    }
}
