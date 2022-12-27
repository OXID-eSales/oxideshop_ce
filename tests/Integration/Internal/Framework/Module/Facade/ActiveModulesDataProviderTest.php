<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ActiveModulesDataProviderTest extends TestCase
{
    use ContainerTrait;

    private $activeModuleId = 'activeModuleId';
    private $activeModulePath = 'some-path-active';
    private $activeModuleSource = 'some-source-active';
    private $inactiveModuleId = 'inActiveModuleId';
    private $inactiveModulePath = 'some-path-inactive';
    private $inactiveModuleSource = 'some-source-inactive';

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
                $this->activeModuleId => Path::join($this->context->getShopRootPath(), $this->activeModuleSource)
            ],
            $this->get(ActiveModulesDataProviderInterface::class)->getModulePaths()
        );
    }

    public function testGetModulePathsUsesCacheIfItExists(): void
    {
        $cache = $this->getDummyCache();
        $cache->put('absolute_module_paths', 1, ['moduleId' => 'somePath']);

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
                $this->activeModuleId => Path::join($this->context->getShopRootPath(), $this->activeModuleSource)
            ],
            $activeModulesDataProvider->getModulePaths()
        );
    }

    public function testGetControllers(): void
    {
        $activeModulesDataProvider = $this->getActiveModulesDataProviderWithCache($this->get(ModuleCacheServiceInterface::class));

        $this->assertEquals(
            [
                new ModuleConfiguration\Controller('activeController1', 'activeControllerNamespace1'),
                new ModuleConfiguration\Controller('activeController2', 'activeControllerNamespace2')
            ],
            $activeModulesDataProvider->getControllers()
        );
    }

    private function prepareTestShopConfiguration(): void
    {
        $activeModule = new ModuleConfiguration();
        $activeModule
            ->setId($this->activeModuleId)
            ->setModuleSource($this->activeModuleSource)
            ->addController(new ModuleConfiguration\Controller('activeController1', 'activeControllerNamespace1'))
            ->addController(new ModuleConfiguration\Controller('activeController2', 'activeControllerNamespace2'));

        $inactiveModule = new ModuleConfiguration();
        $inactiveModule
            ->setId($this->inactiveModuleId)
            ->setModuleSource($this->inactiveModuleSource)
            ->addController(new ModuleConfiguration\Controller('inactiveController', 'inactiveControllerNamespace'));

        /** @var ShopConfigurationDaoInterface $dao */
        $dao = $this->get(ShopConfigurationDaoInterface::class);
        $shopConfiguration = $dao->get(1);
        $shopConfiguration
            ->addModuleConfiguration($activeModule)
            ->addModuleConfiguration($inactiveModule);

        $dao->save($shopConfiguration, $this->context->getDefaultShopId());

        $this->get(ModuleActivationServiceInterface::class)->activate($this->activeModuleId, $this->context->getDefaultShopId());
    }

    private function cleanUpTestData(): void
    {
        $this->get(ModuleActivationServiceInterface::class)->deactivate($this->activeModuleId, $this->context->getDefaultShopId());
    }

    private function getActiveModulesDataProviderWithCache(ModuleCacheServiceInterface $cache): ActiveModulesDataProvider
    {
        return new ActiveModulesDataProvider(
            $this->get(ModuleConfigurationDaoInterface::class),
            $this->get(ModulePathResolverInterface::class),
            $this->get(ContextInterface::class),
            $cache
        );
    }

    private function getDummyCache(): ModuleCacheServiceInterface
    {
        return new class implements ModuleCacheServiceInterface {
            private $cache;

            public function invalidate(string $moduleId, int $shopId): void
            {
            }

            public function put(string $key, int $shopId, array $data): void
            {
                $this->cache[$shopId][$key] = $data;
            }

            public function get(string $key, int $shopId): array
            {
                return $this->cache[$shopId][$key];
            }

            public function exists(string $key, int $shopId): bool
            {
                return isset($this->cache[$shopId][$key]);
            }
        };
    }
}
