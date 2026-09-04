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
use OxidEsales\EshopCommunity\Tests\TestContainerFactory;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class BootstrapModuleInstallerTest extends TestCase
{
    private const MARKER_SERVICE_ID = 'oxid_esales.tests.module_bootstrap_services_marker';

    private ContainerInterface $container;
    private BasicContextInterface $context;
    private string $modulePath;
    private string $generatedServicesDirectory;
    private string $generatedServicesFilePath;

    public function setUp(): void
    {
        parent::setUp();

        $this->modulePath = realpath(__DIR__ . '/Fixtures/ModuleWithBootstrapServices');
        $this->generatedServicesDirectory = Path::join(sys_get_temp_dir(), 'oxid-module-bootstrap-services-test');
        $this->generatedServicesFilePath = Path::join($this->generatedServicesDirectory, 'generated_services.yaml');

        $this->container = $this->createContainerWithRedirectedGeneratedServicesFile();
    }

    public function tearDown(): void
    {
        $this->container->get(ModuleInstallerInterface::class)->uninstall(new OxidEshopPackage($this->modulePath));
        (new Filesystem())->remove($this->generatedServicesDirectory);

        parent::tearDown();
    }

    public function testInstallImportsModuleBootstrapServices(): void
    {
        $this->container->get(ModuleInstallerInterface::class)->install(new OxidEshopPackage($this->modulePath));

        $this->assertStringContainsString(
            'ModuleWithBootstrapServices/bootstrap-services.yaml',
            $this->getGeneratedServicesFileContents()
        );
        $this->assertTrue($this->buildContainerFromGeneratedServices()->has(self::MARKER_SERVICE_ID));
    }

    public function testUninstallRemovesModuleBootstrapServices(): void
    {
        $installer = $this->container->get(ModuleInstallerInterface::class);
        $package = new OxidEshopPackage($this->modulePath);

        $installer->install($package);
        $installer->uninstall($package);

        $this->assertStringNotContainsString(
            'ModuleWithBootstrapServices/bootstrap-services.yaml',
            $this->getGeneratedServicesFileContents()
        );
        $this->assertFalse($this->buildContainerFromGeneratedServices()->has(self::MARKER_SERVICE_ID));
    }

    private function createContainerWithRedirectedGeneratedServicesFile(): ContainerInterface
    {
        $container = (new TestContainerFactory())->create();

        $this->context = $container->get(BasicContextInterface::class);
        $this->context->setGeneratedServicesFilePath($this->generatedServicesFilePath);
        $container->set(BasicContextInterface::class, $this->context);
        $container->autowire(BasicContextInterface::class, BasicContextStub::class);

        $container->compile();

        return $container;
    }

    private function buildContainerFromGeneratedServices(): ContainerInterface
    {
        $container = (new ContainerBuilder($this->context))->getContainer();
        $container->compile();

        return $container;
    }

    private function getGeneratedServicesFileContents(): string
    {
        return file_exists($this->generatedServicesFilePath)
            ? file_get_contents($this->generatedServicesFilePath)
            : '';
    }
}
