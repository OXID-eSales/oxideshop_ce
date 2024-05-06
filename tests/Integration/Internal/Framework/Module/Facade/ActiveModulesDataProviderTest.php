<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ClassExtensionsChain;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ActiveClassExtensionChainResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ActiveModulesDataProviderTest extends TestCase
{
    use ContainerTrait;

    private string $activeModuleId = 'activeModuleId';
    private string $activeModulePath = 'some-path-active';
    private string $activeModuleSource = 'some-source-active';
    private string $inactiveModuleId = 'inActiveModuleId';
    private string $inactiveModulePath = 'some-path-inactive';
    private string $inactiveModuleSource = 'some-source-inactive';

    private BasicContext $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = new BasicContext();
        $this->prepareTestShopConfiguration();
    }

    protected function tearDown(): void
    {
        $this->cleanUpTestData();

        parent::tearDown();
    }

    public function testGetModuleIds(): void
    {
        $this->assertSame(
            [$this->activeModuleId],
            $this->get(ActiveModulesDataProviderInterface::class)->getModuleIds()
        );
    }

    public function testGetModulePathsWillReturnSourcePathForActiveModule(): void
    {
        $this->assertEquals(
            [
                $this->activeModuleId => Path::join($this->context->getShopRootPath(), $this->activeModuleSource),
            ],
            $this->get(ActiveModulesDataProviderInterface::class)->getModulePaths()
        );
    }

    #[Group('cache')]
    public function testGetModulePathsUsesCacheIfItExists(): void
    {
        $cache = $this->getDummyCache();
        $cache->put('absolute_module_paths', ['moduleId' => 'somePath']);

        $activeModulesDataProvider = $this->getActiveModulesDataProviderWithCache($cache);

        $this->assertEquals(
            ['moduleId' => 'somePath'],
            $activeModulesDataProvider->getModulePaths()
        );
    }

    public function testGetModulePathsUsesCacheIfItDoesNotExist(): void
    {
        $activeModulesDataProvider = $this->getActiveModulesDataProviderWithCache($this->getDummyCache());

        $this->assertEquals(
            [
                $this->activeModuleId => Path::join($this->context->getShopRootPath(), $this->activeModuleSource),
            ],
            $activeModulesDataProvider->getModulePaths()
        );
    }

    public function testGetControllers(): void
    {
        $activeModulesDataProvider =
            $this->getActiveModulesDataProviderWithCache($this->get(ModuleCacheInterface::class));

        $this->assertEquals(
            [
                new ModuleConfiguration\Controller('activeController1', 'activeControllerNamespace1'),
                new ModuleConfiguration\Controller('activeController2', 'activeControllerNamespace2'),
            ],
            $activeModulesDataProvider->getControllers()
        );
    }

    public function testGetModuleClassExtensionsIfCacheDoesNotExist(): void
    {
        $activeModulesDataProvider =
            $this->getActiveModulesDataProviderWithCache($this->get(ModuleCacheInterface::class));

        $this->assertEquals(
            [
                'shopClass'        => ['moduleExtensionClassName1'],
                'anotherShopClass' => ['moduleExtensionClassName2'],
            ],
            $activeModulesDataProvider->getClassExtensions()
        );
    }

    public function testGetModuleClassExtensionsUsesCacheIfItExists(): void
    {
        $cache = $this->getDummyCache();
        $cache->put(
            'module_class_extensions',
            [
                'shopClassCache'        => ['moduleExtensionClassName1'],
                'anotherShopClassCache' => ['moduleExtensionClassName2'],
            ]
        );

        $activeModulesDataProvider = $this->getActiveModulesDataProviderWithCache($cache);

        $this->assertEquals(
            [
                'shopClassCache'        => ['moduleExtensionClassName1'],
                'anotherShopClassCache' => ['moduleExtensionClassName2'],
            ],
            $activeModulesDataProvider->getClassExtensions()
        );
    }

    private function prepareTestShopConfiguration(): void
    {
        $activeModule = new ModuleConfiguration();
        $activeModule
            ->setId($this->activeModuleId)
            ->setModuleSource($this->activeModuleSource)
            ->addController(new ModuleConfiguration\Controller('activeController1', 'activeControllerNamespace1'))
            ->addController(new ModuleConfiguration\Controller('activeController2', 'activeControllerNamespace2'))
            ->addClassExtension(new ModuleConfiguration\ClassExtension('shopClass', 'moduleExtensionClassName1'))
            ->addClassExtension(
                new ModuleConfiguration\ClassExtension(
                    'anotherShopClass',
                    'moduleExtensionClassName2'
                )
            );

        $chain = new ClassExtensionsChain();
        $chain->addExtension(new ModuleConfiguration\ClassExtension('shopClass', 'moduleExtensionClassName1'));
        $chain->addExtension(new ModuleConfiguration\ClassExtension('anotherShopClass', 'moduleExtensionClassName2'));
        $chain->setChain([
            'shopClass'        => ['moduleExtensionClassName1'],
            'anotherShopClass' => ['moduleExtensionClassName2'],
        ]);

        $inactiveModule = new ModuleConfiguration();
        $inactiveModule
            ->setId($this->inactiveModuleId)
            ->setModuleSource($this->inactiveModuleSource)
            ->addController(new ModuleConfiguration\Controller('inactiveController', 'inactiveControllerNamespace'));

        /** @var ShopConfigurationDaoInterface $dao */
        $dao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfiguration = $dao->get(1);
        $shopConfiguration
            ->setClassExtensionsChain($chain)
            ->addModuleConfiguration($activeModule)
            ->addModuleConfiguration($inactiveModule);

        $dao->save($shopConfiguration, $this->context->getDefaultShopId());

        $this->get(ModuleActivationServiceInterface::class)
            ->activate($this->activeModuleId, $this->context->getDefaultShopId());
    }

    private function cleanUpTestData(): void
    {
        $this->get(ModuleActivationServiceInterface::class)
            ->deactivate($this->activeModuleId, $this->context->getDefaultShopId());
    }

    private function getActiveModulesDataProviderWithCache(ModuleCacheInterface $cache): ActiveModulesDataProvider
    {
        return new ActiveModulesDataProvider(
            $this->get(ModuleConfigurationDaoInterface::class),
            $this->get(ModulePathResolverInterface::class),
            $this->get(ContextInterface::class),
            $cache,
            $this->get(ActiveClassExtensionChainResolverInterface::class)
        );
    }

    private function getDummyCache(): ModuleCacheInterface
    {
        return new class implements ModuleCacheInterface {
            private array $cache;

            public function deleteItem(string $key): void
            {
            }

            public function put(string $key, array $data): void
            {
                $this->cache[$key] = $data;
            }

            public function get(string $key): array
            {
                return $this->cache[$key];
            }

            public function exists(string $key): bool
            {
                return isset($this->cache[$key]);
            }
        };
    }
}
