<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Install\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class BootstrapModuleInstallerTest extends TestCase
{
    use ContainerTrait;

    private const MARKER_SERVICE_ID = 'oxid_esales.tests.module_bootstrap_services_marker';

    private string $modulePath;
    private string $moduleWithoutBootstrapServicesPath;
    private string $generatedServicesDirectory;
    private string $generatedServicesFilePath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->modulePath = realpath(__DIR__ . '/Fixtures/ModuleWithBootstrapServices');
        $this->moduleWithoutBootstrapServicesPath = realpath(__DIR__ . '/Fixtures/myTestModule');
        $this->generatedServicesDirectory = Path::join(sys_get_temp_dir(), 'oxid-module-bootstrap-services-test');
        $this->generatedServicesFilePath = Path::join($this->generatedServicesDirectory, 'generated_services.yaml');

        $this->createContainer();
        $this->get(BasicContextInterface::class)->setGeneratedServicesFilePath($this->generatedServicesFilePath);
        $this->compileContainer();
    }

    protected function tearDown(): void
    {
        $this->get(ModuleInstallerInterface::class)->uninstall(new OxidEshopPackage($this->modulePath));
        (new Filesystem())->remove($this->generatedServicesDirectory);

        parent::tearDown();
    }

    public function testInstallImportsModuleBootstrapServices(): void
    {
        $this->get(ModuleInstallerInterface::class)->install(new OxidEshopPackage($this->modulePath));

        $this->assertTrue($this->buildContainerFromGeneratedServices()->has(self::MARKER_SERVICE_ID));
    }

    public function testUninstallRemovesModuleBootstrapServices(): void
    {
        $installer = $this->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage($this->modulePath);

        $installer->install($package);
        $installer->uninstall($package);

        $this->assertFalse($this->buildContainerFromGeneratedServices()->has(self::MARKER_SERVICE_ID));
    }

    public function testInstallModuleWithoutBootstrapServices(): void
    {
        $installer = $this->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage($this->moduleWithoutBootstrapServicesPath);

        $installer->install($package);

        $this->assertFalse($this->buildContainerFromGeneratedServices()->has(self::MARKER_SERVICE_ID));

        $installer->uninstall($package);
    }

    private function buildContainerFromGeneratedServices(): ContainerInterface
    {
        $container = (new ContainerBuilder($this->get(BasicContextInterface::class)))->getContainer();
        $container->compile();

        return $container;
    }
}
