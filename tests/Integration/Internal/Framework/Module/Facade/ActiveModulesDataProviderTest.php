<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Framework\Module\Facade;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Facade\ActiveModulesDataProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Path\ModulePathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Service\ModuleActivationServiceInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\State\ModuleStateServiceInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

final class ActiveModulesDataProviderTest extends TestCase
{
    use ContainerTrait;

    private $activeModuleId = 'activeModuleId';
    private $activeModulePath = 'some-path-active';
    private $activeModuleSource = 'some-source-active';
    private $inactiveModuleId = 'inActiveModuleId';
    private $inactiveModulePath = 'some-path-inactive';
    private $inactiveModuleSource = 'some-source-inactive';

    /** @var BasicContext */
    private $context;

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

    public function testGetTemplates(): void
    {
        $this->assertEquals(
            [
                $this->activeModuleId => [
                    new ModuleConfiguration\Template('activeTemplate', 'activeTemplatePath'),
                ]
            ],
            $this->get(ActiveModulesDataProviderInterface::class)->getTemplates()
        );
    }

    public function testGetTemplatesUsesCacheIfItExists(): void
    {
        $cachedData = [new ModuleConfiguration\Template('key', 'path')];
        $cache = $this->getDummyCache();
        $cache->put('templates', 1, $cachedData);

        $activeModulesDataProvider = $this->getActiveModulesDataProviderWithCache($cache);

        $this->assertEquals(
            $cachedData,
            $activeModulesDataProvider->getTemplates()
        );
    }

    public function testGetTemplatesUsesCacheIfItDoesNotExist(): void
    {
        $activeModulesDataProvider = $this->getActiveModulesDataProviderWithCache($this->getDummyCache());

        $this->assertEquals(
            [
                $this->activeModuleId => [
                    new ModuleConfiguration\Template('activeTemplate', 'activeTemplatePath'),
                ]
            ],
            $activeModulesDataProvider->getTemplates()
        );
    }

    private function prepareTestShopConfiguration(): void
    {
        $activeModule = new ModuleConfiguration();
        $activeModule
            ->setId($this->activeModuleId)
            ->setPath($this->activeModulePath)
            ->setModuleSource($this->activeModuleSource)
            ->addTemplate(new ModuleConfiguration\Template('activeTemplate', 'activeTemplatePath'));

        $inactiveModule = new ModuleConfiguration();
        $inactiveModule
            ->setId($this->inactiveModuleId)
            ->setPath($this->inactiveModulePath)
            ->setModuleSource($this->inactiveModuleSource)
            ->addTemplate(new ModuleConfiguration\Template('inactiveTemplate', 'inactiveTemplatePath'));

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

    private function getActiveModulesDataProviderWithCache(ModuleCacheServiceInterface $cache
    ): ActiveModulesDataProvider {
        return new ActiveModulesDataProvider(
            $this->get(ShopConfigurationDaoInterface::class),
            $this->get(ModuleStateServiceInterface::class),
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
