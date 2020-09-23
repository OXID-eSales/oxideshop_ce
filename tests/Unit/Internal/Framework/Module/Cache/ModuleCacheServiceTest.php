<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Cache;

use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Cache\ModuleCacheService;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Exception\ModulePathCacheException;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\ShopAdapter;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContext;
use Symfony\Component\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;

final class ModuleCacheServiceTest extends TestCase
{
    /** @var vfsStreamDirectory */
    private $dir;

    /** @var ModuleCacheService */
    private $moduleCacheService;

    protected function setUp(): void
    {
        $this->setVirtualDirectory();

        $this->moduleCacheService = $this->getModuleCacheService();

        parent::setUp();
    }

    private function setVirtualDirectory(): void
    {
        $structure = [
            'tmp' => [
                'modules' => [
                    '1' => []
                ]
            ]
        ];

        $this->dir = vfsStream::setup('root', 0777, $structure);

        $modulePathCacheFile = $this->dir->getChild('tmp/modules/1')->url() . '/module_path_cache.txt';
        file_put_contents($modulePathCacheFile, "");
    }

    private function getModuleCacheService(): ModuleCacheService
    {
        $shopAdapterInterface = $this->getMockBuilder(ShopAdapter::class)->getMock();
        $filesystem = new Filesystem();

        $basicContext = $this->getMockBuilder(BasicContext::class)->getMock();
        $basicContext->method('getModulePathCacheFilePath')
            ->willReturn($this->dir->getChild('tmp/modules/1/module_path_cache.txt')->url());

        return new ModuleCacheService(
            $shopAdapterInterface,
            $filesystem,
            $basicContext
        );
    }

    public function testModulePathCacheFileHasNoPermission(): void
    {
        $this->dir->getChild('tmp/modules/1/module_path_cache.txt')->chmod(0111);

        $this->expectException(ModulePathCacheException::class);

        $this->moduleCacheService->put("1", 1, ['1']);
    }
}
