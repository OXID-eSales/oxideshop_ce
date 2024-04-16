<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleFilesInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ModuleFilesInstallerTest extends TestCase
{
    use ContainerTrait;

    private $modulePackagePath = __DIR__ . '/../../TestData/TestModule';
    private string $packageName = 'TestModule';

    protected function tearDown(): void
    {
        $this->cleanupTestData();

        parent::tearDown();
    }

    public function testModuleNotInstalledByDefault(): void
    {
        $installer = $this->getFilesInstaller();

        $this->assertFalse(
            $installer->isInstalled($this->createPackage())
        );
    }

    public function testModuleIsInstalledAfterInstallProcess(): void
    {
        $installer = $this->getFilesInstaller();
        $package = $this->createPackage();

        $installer->install($package);

        $this->assertTrue($installer->isInstalled($package));
    }

    public function testModuleAssertsAreLinkedAfterInstallProcess(): void
    {
        $installer = $this->getFilesInstaller();
        $package = $this->createPackage();

        $installer->install($package);

        $this->assertFileEquals(
            $this->modulePackagePath . '/assets/some.css',
            $this->getTestModuleAssetsPath() . '/some.css'
        );
    }

    public function testUninstall(): void
    {
        $installer = $this->getFilesInstaller();
        $package = $this->createPackage();
        $installer->install($package);

        $installer->uninstall($package);

        $this->assertFalse($installer->isInstalled($package));
    }

    public function testInstallCreatesRelativeSymlink(): void
    {
        $installer = $this->getFilesInstaller();
        $package = $this->createPackage();

        $installer->install($package);

        $this->assertTrue(Path::isRelative(readlink($this->getTestModuleAssetsPath())));
    }

    private function getFilesInstaller(): ModuleFilesInstallerInterface
    {
        return $this->get(ModuleFilesInstallerInterface::class);
    }

    private function createPackage(): OxidEshopPackage
    {
        return new OxidEshopPackage($this->modulePackagePath);
    }

    private function getTestModuleAssetsPath(): string
    {
        return Path::join(
            $this->get(ContextInterface::class)->getOutPath(),
            'modules',
            'test-module'
        );
    }

    private function cleanupTestData(): void
    {
        $this->getFilesInstaller()->uninstall($this->createPackage());
    }
}
