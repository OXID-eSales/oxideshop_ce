<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\AdminUiPackageInstaller;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class AdminUiPackageInstallerTest extends IntegrationTestCase
{
    use ContainerTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->backupAdminUiAssets();
    }

    public function tearDown(): void
    {
        $this->restoreAdminUiAssets();

        parent::tearDown();
    }

    public function testAdminUiNotInstalledByDefault(): void
    {
        $this->assertFalse($this->getPackageInstaller()->isInstalled($this->getPackagePath()));
    }

    public function testAdminUiIsInstalledAfterInstallProcess(): void
    {
        $installer = $this->getPackageInstaller();

        $installer->install($this->getPackagePath());

        $this->assertTrue($installer->isInstalled($this->getPackagePath()));
    }

    public function testAdminUiIsInstalledAfterUpdateProcess(): void
    {
        $installer = $this->getPackageInstaller();

        $installer->update($this->getPackagePath());

        $this->assertTrue($installer->isInstalled($this->getPackagePath()));
    }

    public function testAdminUiAssetsAreLinkedToOutDirectoryAfterInstallProcess(): void
    {
        $this->getPackageInstaller()->install($this->getPackagePath());

        $this->assertTrue(is_link($this->getAssetsLinkPath()));
        $this->assertSame(
            realpath(Path::join($this->getPackagePath(), 'assets')),
            realpath($this->getAssetsLinkPath())
        );
    }

    public function testAdminUiAssetsLinkIsRemovedAfterUninstallProcess(): void
    {
        $installer = $this->getPackageInstaller();
        $installer->install($this->getPackagePath());

        $installer->uninstall($this->getPackagePath());

        $this->assertFalse(is_link($this->getAssetsLinkPath()));
    }

    public function testAdminUiInstallDoesNotUseMainContainer(): void
    {
        ContainerFactory::resetContainer();
        $this->getPackageInstaller()->install($this->getPackagePath());

        $this->assertFileDoesNotExist(
            $this->get(ContextInterface::class)->getContainerCacheFilePath(
                $this->get(ContextInterface::class)->getCurrentShopId()
            )
        );
    }

    private function backupAdminUiAssets(): void
    {
        if (file_exists($this->getAssetsLinkPath()) || is_link($this->getAssetsLinkPath())) {
            (new Filesystem())->rename($this->getAssetsLinkPath(), $this->getAssetsBackupPath());
        }
    }

    private function restoreAdminUiAssets(): void
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getAssetsLinkPath());
        if (file_exists($this->getAssetsBackupPath()) || is_link($this->getAssetsBackupPath())) {
            $filesystem->rename($this->getAssetsBackupPath(), $this->getAssetsLinkPath());
        }
    }

    private function getPackagePath(): string
    {
        return Path::join(
            $this->get(BasicContextInterface::class)->getVendorPath(),
            $this->getParameter('oxid_esales.admin_ui.package_name')
        );
    }

    private function getAssetsLinkPath(): string
    {
        return Path::join(
            $this->get(BasicContextInterface::class)->getOutPath(),
            $this->getParameter('oxid_esales.admin_ui.name')
        );
    }

    private function getAssetsBackupPath(): string
    {
        return $this->getAssetsLinkPath() . '.testing-backup';
    }

    private function getPackageInstaller(): AdminUiPackageInstaller
    {
        return new AdminUiPackageInstaller(
            new NullIO(),
            $this->get(BasicContextInterface::class)->getSourcePath(),
            new Package($this->getParameter('oxid_esales.admin_ui.package_name'), '1.0.0', '1.0.0')
        );
    }
}
