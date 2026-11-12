<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\AdminUi\Service;

use OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Service\AdminUiAssetsInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class AdminUiAssetsInstallerTest extends IntegrationTestCase
{
    use ContainerTrait;

    private const ADMIN_UI_NAME = 'testAdminUi';

    private string $adminUiDirectory = __DIR__ . '/../Fixtures/adminUi';
    private string $adminUiWithoutAssetsDirectory = __DIR__ . '/../Fixtures/adminUiWithoutAssets';

    public function tearDown(): void
    {
        (new Filesystem())->remove($this->getAssetsLinkPath());

        parent::tearDown();
    }

    public function testAssetsAreNotInstalledByDefault(): void
    {
        $this->assertFalse($this->getInstaller($this->adminUiDirectory)->isInstalled());
    }

    public function testAssetsAreInstalledAfterInstall(): void
    {
        $installer = $this->getInstaller($this->adminUiDirectory);

        $installer->install();

        $this->assertTrue($installer->isInstalled());
    }

    public function testAssetsAreLinkedToOutDirectoryAfterInstall(): void
    {
        $this->getInstaller($this->adminUiDirectory)->install();

        $this->assertFileEquals(
            Path::join($this->adminUiDirectory, 'assets/some.css'),
            Path::join($this->getAssetsLinkPath(), 'some.css')
        );
    }

    public function testInstallCreatesRelativeSymlink(): void
    {
        $this->getInstaller($this->adminUiDirectory)->install();

        $this->assertTrue(Path::isRelative(readlink($this->getAssetsLinkPath())));
    }

    public function testInstallReplacesCopiedAssetsDirectory(): void
    {
        (new Filesystem())->dumpFile(Path::join($this->getAssetsLinkPath(), 'copied.css'), 'body {}');
        $installer = $this->getInstaller($this->adminUiDirectory);

        $installer->install();

        $this->assertTrue($installer->isInstalled());
        $this->assertFileDoesNotExist(Path::join($this->getAssetsLinkPath(), 'copied.css'));
    }

    public function testInstallIsRepeatable(): void
    {
        $installer = $this->getInstaller($this->adminUiDirectory);
        $installer->install();

        $installer->install();

        $this->assertTrue($installer->isInstalled());
    }

    public function testAssetsAreNotInstalledAfterUninstall(): void
    {
        $installer = $this->getInstaller($this->adminUiDirectory);
        $installer->install();

        $installer->uninstall();

        $this->assertFalse($installer->isInstalled());
        $this->assertFileExists(Path::join($this->adminUiDirectory, 'assets/some.css'));
    }

    public function testInstallSkipsAdminUiWithoutAssets(): void
    {
        $installer = $this->getInstaller($this->adminUiWithoutAssetsDirectory);

        $installer->install();

        $this->assertFalse(is_link($this->getAssetsLinkPath()));
        $this->assertFalse($installer->isInstalled());
    }

    public function testBrokenAssetsLinkIsNotInstalled(): void
    {
        (new Filesystem())->symlink('not-existing-directory', $this->getAssetsLinkPath());

        $this->assertFalse($this->getInstaller($this->adminUiDirectory)->isInstalled());
    }

    public function testUninstallRemovesCopiedAssetsDirectory(): void
    {
        (new Filesystem())->dumpFile(Path::join($this->getAssetsLinkPath(), 'copied.css'), 'body {}');

        $this->getInstaller($this->adminUiDirectory)->uninstall();

        $this->assertDirectoryDoesNotExist($this->getAssetsLinkPath());
    }

    private function getInstaller(string $adminUiDirectory): AdminUiAssetsInstallerInterface
    {
        $this->setParameter('oxid_esales.admin_ui.name', self::ADMIN_UI_NAME);
        $this->setParameter(
            'oxid_esales.admin_ui.package_name',
            Path::makeRelative($adminUiDirectory, $this->get(BasicContextInterface::class)->getVendorPath())
        );

        return $this->get(AdminUiAssetsInstallerInterface::class);
    }

    private function getAssetsLinkPath(): string
    {
        return Path::join($this->get(BasicContextInterface::class)->getOutPath(), self::ADMIN_UI_NAME);
    }
}
