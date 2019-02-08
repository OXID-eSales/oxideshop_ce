<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ComposerPlugin;

use Composer\IO\NullIO;
use Composer\Package\Package;
use OxidEsales\ComposerPlugin\Installer\Package\ModulePackageInstaller;
use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

class ModulePackageInstallerTest extends TestCase
{
    use ContainerTrait;

    private $modulePackagePath = __DIR__ . '/Fixtures/test-module-package-installation';
    private $packageName = 'test-module-package-installation';

    public function tearDown()
    {
        $fileSystem = $this->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($this->getModulesPath() . '/' . $this->packageName);

        parent::tearDown();
    }

    public function testModuleNotInstalledByDefault()
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $this->assertFalse($installer->isInstalled($this->packageName));
    }

    public function testModuleIsInstalledAfterInstallProcess()
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $installer->install($this->modulePackagePath);

        $this->assertTrue($installer->isInstalled());
    }

    public function testModuleFilesAreCopiedAfterInstallProcess()
    {
        $installer = $this->getPackageInstaller($this->packageName);
        $installer->install($this->modulePackagePath);

        $this->assertFileEquals(
            $this->modulePackagePath . '/metadata.php',
            $this->getModulesPath() . '/' . $this->packageName . '/metadata.php'
        );
    }

    private function getModulesPath(): string
    {
        return $this->get(ContextInterface::class)->getModulesPath();
    }

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
}
