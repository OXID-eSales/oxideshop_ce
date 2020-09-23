<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\Cache;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheService;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\DataObject\OxidEshopPackage;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ModuleInstallerInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

final class ModuleCacheServiceTest extends TestCase
{
    use ContainerTrait;

    private $moduleId1 = 'testModule1';
    private $moduleId2 = 'testModule2';

    private $modulePathCacheFilePath = __DIR__ . '/Fixtures/tmp/module_path_cache.txt';

    /** @var ModuleCacheService */
    private $moduleCacheService;

    /** @var BasicContext */
    private $basicContext;

    protected function setUp(): void
    {
        parent::setUp();

        $this->installModule($this->moduleId1);
        $this->installModule($this->moduleId2);

        $this->moduleCacheService = $this->getModuleCacheService();

        $this->moduleCacheService->put($this->moduleId1, 1, [__DIR__ . '/Fixtures/testModule1']);
        $this->moduleCacheService->put($this->moduleId2, 1, [__DIR__ . '/Fixtures/testModule2']);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->removeTestModules();
        unlink($this->modulePathCacheFilePath);
    }

    private function getModuleCacheService(): ModuleCacheService
    {
        $shopAdapterInterface = $this->get(ShopAdapterInterface::class);
        $filesystem = new Filesystem();

        $this->basicContext = $this->getMockBuilder(BasicContext::class)->getMock();
        $this->basicContext->method('getModulePathCacheFilePath')->willReturn($this->modulePathCacheFilePath);

        return new ModuleCacheService(
            $shopAdapterInterface,
            $filesystem,
            $this->basicContext
        );
    }

    public function testPutModulePathInCache(): void
    {
        self::assertFileExists($this->basicContext->getModulePathCacheFilePath(1));

        $cacheContent = file_get_contents($this->basicContext->getModulePathCacheFilePath(1));

        $expected = [
            $this->moduleId1 => [__DIR__ . '/Fixtures/' . $this->moduleId1],
            $this->moduleId2 => [__DIR__ . '/Fixtures/' . $this->moduleId2],
        ];

        self::assertEquals($cacheContent, serialize($expected));
    }

    public function testGetModulePathCacheFromCache(): void
    {
        $modulePathCacheFileContent = $this->moduleCacheService->get($this->moduleId1, 1);

        $expected = [__DIR__ . '/Fixtures/' . $this->moduleId1];

        self::assertEquals($modulePathCacheFileContent, $expected);
    }

    public function testRemoveModulePathCacheFromCache(): void
    {
        $this->moduleCacheService->evict($this->moduleId1, 1);

        $modulePathCache = $this->moduleCacheService->get($this->moduleId1, 1);

        self::assertEquals($modulePathCache, []);
    }

    public function testIfModulePathExistInCache(): void
    {
        self::assertTrue($this->moduleCacheService->exists($this->moduleId1, 1));

        $this->moduleCacheService->evict($this->moduleId2, 1);
        self::assertFalse($this->moduleCacheService->exists($this->moduleId2, 1));
    }

    private function installModule(string $moduleId): void
    {
        $installService = $this->get(ModuleInstallerInterface::class);

        $package = new OxidEshopPackage($moduleId, __DIR__ . '/Fixtures/' . $moduleId);
        $package->setTargetDirectory('oeTest/' . $moduleId);
        $installService->install($package);
    }

    private function removeTestModules(): void
    {
        $fileSystem = $this->get('oxid_esales.symfony.file_system');
        $fileSystem->remove($this->get(ContextInterface::class)->getModulesPath() . '/oeTest/');
    }
}
