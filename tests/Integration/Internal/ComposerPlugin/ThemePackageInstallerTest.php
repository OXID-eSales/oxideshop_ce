<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\BufferIO;
use Composer\IO\IOInterface;
use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\ThemePackageInstaller;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Configuration\Dao\ThemeConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

final class ThemePackageInstallerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $themePackagePath = __DIR__ . '/Fixtures/test-theme-package-installation';
    private string $themePackageWithoutAssetsPath = __DIR__ . '/Fixtures/test-theme-package-without-assets';
    private string $themePackageWithoutMetadataPath = __DIR__ . '/Fixtures/test-theme-package-without-metadata';
    private string $themePackageWithInvalidMetadataPath = __DIR__ . '/Fixtures/test-theme-package-with-invalid-metadata';
    private string $packageName = 'test-vendor/test-theme-package-installation';
    private string $themeId = 'test-theme-package-installation';

    public function tearDown(): void
    {
        (new Filesystem())->remove($this->getAssetsLinkPath());

        parent::tearDown();
    }

    public function testThemeNotInstalledByDefault(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);

        $this->assertFalse($installer->isInstalled($this->themePackagePath));
    }

    public function testThemeIsInstalledAfterInstallProcess(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);

        $installer->install($this->themePackagePath);

        $this->assertTrue($installer->isInstalled($this->themePackagePath));
    }

    public function testThemeWithoutAssetsIsInstalledAfterInstallProcess(): void
    {
        $installer = $this->getPackageInstaller('test-vendor/test-theme-package-without-assets');

        $installer->install($this->themePackageWithoutAssetsPath);

        $this->assertTrue($installer->isInstalled($this->themePackageWithoutAssetsPath));
    }

    public function testThemeIsInstalledAfterUpdateProcess(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);

        $installer->update($this->themePackagePath);

        $this->assertTrue($installer->isInstalled($this->themePackagePath));
    }

    public function testInstalledThemeConfigurationIsUpdatedAfterUpdateProcess(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $dao = $this->get(ThemeConfigurationDaoInterface::class);
        $context = $this->get(BasicContextInterface::class);
        $shopId = $context->getDefaultShopId();
        $installer->install($this->themePackagePath);
        $configuration = $dao->get($this->themeId, $shopId);
        $configuration->setTitle('Old Title');
        $configuration->setSource('old/source/path');
        $dao->save($configuration, $shopId);

        $installer->update($this->themePackagePath);

        $configuration = $dao->get($this->themeId, $shopId);
        $this->assertSame('Test Theme Package Installation', $configuration->getTitle());
        $this->assertSame(
            Path::makeRelative($this->themePackagePath, $context->getShopRootPath()),
            $configuration->getSource()
        );
    }

    public function testThemeConfigurationIsInstalledAfterInstallProcess(): void
    {
        $this->getPackageInstaller($this->packageName)->install($this->themePackagePath);

        $this->assertTrue(
            $this->get(ThemeConfigurationDaoInterface::class)->exists(
                $this->themeId,
                $this->get(BasicContextInterface::class)->getDefaultShopId()
            )
        );
    }

    public function testThemeAssetsAreLinkedToOutDirectoryAfterInstallProcess(): void
    {
        $this->getPackageInstaller($this->packageName)->install($this->themePackagePath);

        $this->assertTrue(is_link($this->getAssetsLinkPath()));
        $this->assertFileEquals(
            Path::join($this->themePackagePath, 'assets/some.css'),
            Path::join($this->getAssetsLinkPath(), 'some.css')
        );
    }

    public function testThemeFilesAreNotCopiedToShopSourceAfterInstallProcess(): void
    {
        $this->getPackageInstaller($this->packageName)->install($this->themePackagePath);

        $this->assertDirectoryDoesNotExist(
            Path::join(
                $this->get(BasicContextInterface::class)->getSourcePath(),
                'Application/views',
                $this->themeId
            )
        );
    }

    public function testThemeInstallDoesNotUseMainContainer(): void
    {
        ContainerFactory::resetContainer();
        $this->getPackageInstaller($this->packageName)->install($this->themePackagePath);

        $this->assertFileDoesNotExist(
            $this->get(ContextInterface::class)->getContainerCacheFilePath(
                $this->get(ContextInterface::class)->getCurrentShopId()
            )
        );
    }

    public function testThemeUpdateDoesNotUseMainContainer(): void
    {
        ContainerFactory::resetContainer();
        $this->getPackageInstaller($this->packageName)->update($this->themePackagePath);

        $this->assertFileDoesNotExist(
            $this->get(ContextInterface::class)->getContainerCacheFilePath(
                $this->get(ContextInterface::class)->getCurrentShopId()
            )
        );
    }

    public function testThemeWithoutMetadataIsNotInstalled(): void
    {
        $installer = $this->getPackageInstaller('test-vendor/test-theme-package-without-metadata');

        $installer->install($this->themePackageWithoutMetadataPath);

        $this->assertFalse($installer->isInstalled($this->themePackageWithoutMetadataPath));
    }

    public function testThemeWithInvalidMetadataIsNotInstalled(): void
    {
        $installer = $this->getPackageInstaller('test-vendor/test-theme-package-with-invalid-metadata');

        $installer->install($this->themePackageWithInvalidMetadataPath);

        $this->assertFalse($installer->isInstalled($this->themePackageWithInvalidMetadataPath));
    }

    public function testWarningWithPackageNameIsWrittenForThemeWithoutMetadata(): void
    {
        $io = new BufferIO();

        $this->getPackageInstaller('test-vendor/test-theme-package-without-metadata', $io)
            ->install($this->themePackageWithoutMetadataPath);

        $this->assertStringContainsString(
            'Theme package test-vendor/test-theme-package-without-metadata is skipped',
            $io->getOutput()
        );
    }

    public function testThemeWithoutMetadataIsSkippedOnUninstall(): void
    {
        $io = new BufferIO();

        $this->getPackageInstaller('test-vendor/test-theme-package-without-metadata', $io)
            ->uninstall($this->themePackageWithoutMetadataPath);

        $this->assertStringContainsString(
            'Theme package test-vendor/test-theme-package-without-metadata is skipped',
            $io->getOutput()
        );
    }

    public function testThemeConfigurationIsUninstalledAfterUninstallProcess(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $installer->install($this->themePackagePath);

        $installer->uninstall($this->themePackagePath);

        $this->assertFalse(
            $this->get(ThemeConfigurationDaoInterface::class)->exists(
                $this->themeId,
                $this->get(BasicContextInterface::class)->getDefaultShopId()
            )
        );
    }

    public function testThemeAssetsLinkIsRemovedAfterUninstallProcess(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $installer->install($this->themePackagePath);

        $installer->uninstall($this->themePackagePath);

        $this->assertFileDoesNotExist($this->getAssetsLinkPath());
    }

    public function testThemeCacheIsClearedAfterUninstallProcess(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $installer->install($this->themePackagePath);
        $cache = $this->get(TagAwareCacheInterface::class);
        $cache->get('theme_uninstall_cache_probe', fn (): string => 'cached');

        $installer->uninstall($this->themePackagePath);

        $this->assertSame('recomputed', $cache->get('theme_uninstall_cache_probe', fn (): string => 'recomputed'));
    }

    private function getAssetsLinkPath(): string
    {
        return Path::join($this->get(BasicContextInterface::class)->getOutPath(), $this->themeId);
    }

    private function getPackageInstaller(
        string $packageName,
        IOInterface $io = new NullIO()
    ): ThemePackageInstaller {
        return new ThemePackageInstaller(
            $io,
            $this->get(BasicContextInterface::class)->getSourcePath(),
            new Package($packageName, '1.0.0', '1.0.0')
        );
    }
}
