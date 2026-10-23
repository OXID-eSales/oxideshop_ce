<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Theme\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Theme\Install\Service\ThemeAssetsInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class ThemeAssetsInstallerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const THEME_ID = 'assetsTestTheme';

    private string $themePath = __DIR__ . '/../Fixtures/assetsTheme';

    public function tearDown(): void
    {
        (new Filesystem())->remove($this->getAssetsLinkPath());

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

    private function getAssetsInstaller(): ThemeAssetsInstallerInterface
    {
        return $this->get(ThemeAssetsInstallerInterface::class);
    }

    private function getAssetsLinkPath(): string
    {
        return Path::join($this->get(BasicContextInterface::class)->getOutPath(), self::THEME_ID);
    }
}
