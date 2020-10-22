<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\ModulePackageInstaller;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Bridge\ModuleActivationBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class ModulePackageInstallerTest extends TestCase
{
    use ContainerTrait;

    private $modulePackagePath = __DIR__ . '/Fixtures/test-module-package-installation';
    private $packageName = 'test-module-package-installation';
    private $moduleId = 'testModule';

    public function tearDown(): void
    {
        $fileSystem = $this->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($this->getModulesPath() . '/' . $this->packageName);

        parent::tearDown();
    }

    public function testModuleNotInstalledByDefault(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $this->assertFalse($installer->isInstalled());
    }

    public function testModuleIsInstalledAfterInstallProcess()
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $installer->install($this->modulePackagePath);

        $this->assertTrue($installer->isInstalled());
    }

    public function testModuleFilesAreCopiedAfterInstallProcess(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $installer->install($this->modulePackagePath);

        $this->assertFileEquals(
            $this->modulePackagePath . '/metadata.php',
            $this->getModulesPath() . '/' . $this->packageName . '/metadata.php'
        );
    }

    public function testModuleUninstall(): void
    {
        $package = new OxidEshopPackage($this->moduleId, __DIR__ . '/Fixtures/' . $this->packageName);
        $package->setTargetDirectory('oeTest/' . $this->moduleId);

        $installer = $this->getPackageInstaller($this->packageName);

        $installer->install($this->modulePackagePath);
        $this->activateTestModule($package);
        $installer->uninstall($this->modulePackagePath);

        $this->assertFalse($installer->isInstalled());
    }

    public function testModuleInstallDoesNotUseMainContainer(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);

        ContainerFactory::resetContainer();
        $installer->install($this->modulePackagePath);

        $this->assertFileNotExists(
            $this->get(ContextInterface::class)->getContainerCacheFilePath()
        );
    }

    public function testModuleUpdateDoesNotUseMainContainer(): void
    {
        $installer = $this->getPackageInstaller($this->packageName);

        ContainerFactory::resetContainer();
        $installer->update($this->modulePackagePath);

        $this->assertFileNotExists(
            $this->get(ContextInterface::class)->getContainerCacheFilePath()
        );
    }

    /**
     * @return string
     */
    private function getModulesPath(): string
    {
        return $this->get(ContextInterface::class)->getModulesPath();
    }

    /**
     * @param string $packageName
     * @param array  $extra
     *
     * @return ModulePackageInstaller
     */
    private function getPackageInstaller(string $packageName, array $extra = []): ModulePackageInstaller
    {
        $package = new Package($packageName, '1.0.0', '1.0.0');
        $package->setExtra($extra);

        return new ModulePackageInstaller(
            new NullIO(),
            $this->get(BasicContextInterface::class)->getSourcePath(),
            $package
        );
    }

    /**
     * @param OxidEshopPackage $package
     */
    private function activateTestModule(OxidEshopPackage $package): void
    {
        $this->get(ModuleInstallerInterface::class)
            ->install($package);
        $this
            ->get(ModuleActivationBridgeInterface::class)
            ->activate($this->moduleId, 1);
    }
}
