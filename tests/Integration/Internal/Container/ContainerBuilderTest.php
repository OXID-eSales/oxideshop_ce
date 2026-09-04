<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Container;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\Facts\Edition\EditionSelector;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;
use Symfony\Component\Yaml\Yaml;

class ContainerBuilderTest extends TestCase
{
    private const MODULE_BOOTSTRAP_SERVICES_MARKER = 'oxid_esales.tests.module_bootstrap_services_marker';

    private Filesystem $filesystem;
    private string $installedModulesConfigurationDirectory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->filesystem = new Filesystem();
        $this->installedModulesConfigurationDirectory = Path::join(
            sys_get_temp_dir(),
            'oxid-container-builder-bootstrap-services-test'
        );
        $this->filesystem->remove($this->installedModulesConfigurationDirectory);
    }

    protected function tearDown(): void
    {
        $this->filesystem->remove($this->installedModulesConfigurationDirectory);

        parent::tearDown();
    }

    public function testWhenCeServicesLoaded(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $container = $this->makeContainer($context);

        $this->assertSame('CE service!', $container->get('oxid_esales.tests.internal.dummy_executor')->execute());
    }

    public function testWhenPeOverwritesMainServices(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::PROFESSIONAL);
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for PE!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testWhenEeOverwritesMainServices(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::ENTERPRISE);
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for EE!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testWhenProjectOverwritesMainServices(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $context->setGeneratedServicesFilePath(__DIR__ . '/Fixtures/Project/generated_services.yaml');
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for Project!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testWhenProjectOverwritesEditions(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::ENTERPRISE);
        $context->setConfigurableServicesFilePath(__DIR__ . '/Fixtures/Project/configurable_services.yaml');
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for Project!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testWhenShopRelatedConfigOverwritesMainServices(): void
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $context->setShopConfigurableServicesFilePath(
            __DIR__ . '/Fixtures/Project/shop_configurable_services.yaml'
        );
        $container = $this->makeContainer($context);

        $this->assertSame(
            'Service overwriting for Project!',
            $container->get('oxid_esales.tests.internal.dummy_executor')->execute()
        );
    }

    public function testBootstrapServicesOfInactiveInstalledModuleAreLoaded(): void
    {
        $context = $this->makeContextStubWithInstalledModules();
        $this->givenInstalledModule('ModuleWithBootstrapServices');

        $this->assertTrue($this->makeContainer($context)->has(self::MODULE_BOOTSTRAP_SERVICES_MARKER));
    }

    public function testNoBootstrapServicesLoadedWithoutInstalledModules(): void
    {
        $context = $this->makeContextStubWithInstalledModules();

        $this->assertFalse($this->makeContainer($context)->has(self::MODULE_BOOTSTRAP_SERVICES_MARKER));
    }

    public function testInstalledModuleWithoutBootstrapServicesDoesNotBreakContainer(): void
    {
        $context = $this->makeContextStubWithInstalledModules();
        $this->givenInstalledModule('TestModule');

        $this->assertFalse($this->makeContainer($context)->has(self::MODULE_BOOTSTRAP_SERVICES_MARKER));
    }

    private function makeContainer(ContextInterface $context): Container
    {
        $containerBuilder = new ContainerBuilder($context);
        $container = $containerBuilder->getContainer();
        $container->compile();
        return $container;
    }

    private function makeContextStubWithInstalledModules(): ContextStub
    {
        $context = $this->makeContextStub();
        $context->setEdition(EditionSelector::COMMUNITY);
        $context->setCurrentShopId(1);
        $context->setProjectConfigurationDirectory($this->installedModulesConfigurationDirectory);

        return $context;
    }

    private function givenInstalledModule(string $fixtureModuleName): void
    {
        $modulesDirectory = Path::join($this->installedModulesConfigurationDirectory, 'shops', '1', 'modules');
        $this->filesystem->mkdir($modulesDirectory);

        $moduleSource = Path::makeRelative(
            realpath(__DIR__ . '/../Framework/Module/TestData/' . $fixtureModuleName),
            $this->makeContextStub()->getShopRootPath()
        );

        $this->filesystem->dumpFile(
            Path::join($modulesDirectory, $fixtureModuleName . '.yaml'),
            Yaml::dump(['moduleSource' => $moduleSource, 'activated' => false])
        );
    }

    private function makeContextStub(): ContextStub
    {
        $context = new ContextStub();
        $context->setCommunityEditionSourcePath(__DIR__ . '/Fixtures/CE');
        $context->setProfessionalEditionRootPath(__DIR__ . '/Fixtures/PE');
        $context->setEnterpriseEditionRootPath(__DIR__ . '/Fixtures/EE');
        $context->setGeneratedServicesFilePath('nonexisting.yaml');
        $context->setConfigurableServicesFilePath('nonexisting.yaml');
        $context->setShopConfigurableServicesFilePath('nonexisting.yaml');
        $context->setActiveModuleServicesFilePath('nonexisting.yaml');
        $context->setProjectConfigurationDirectory(__DIR__ . '/Fixtures/nonexisting');
        return $context;
    }
}
