<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Utility;

use OxidEsales\EshopCommunity\Internal\Container\BootstrapContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\Facts\Config\ConfigFile;
use PHPUnit\Framework\TestCase;
use Webmozart\PathUtil\Path;

final class BasicContextTest extends TestCase
{
    use ContainerTrait;

    /**
     * @var BasicContextInterface
     */
    private $basicContext;

    protected function setup(): void
    {
        $this->basicContext = BootstrapContainerFactory::getBootstrapContainer()->get(BasicContextInterface::class);

        parent::setUp();
    }

    public function testGetConfigFilePath()
    {
        $this->assertFileExists($this->basicContext->getConfigFilePath());
    }

    public function testGetDefaultShopId()
    {
        $this->assertSame(1, $this->basicContext->getDefaultShopId());
    }

    public function testGetModulePathCacheFilePath()
    {
        $getModulePathCacheFilePath = $this->basicContext->getModulePathCacheFilePath(1);

        $expected = Path::join(
            (new ConfigFile())->getVar('sCompileDir'),
            'modules/1/module_path_cache.txt'
        );

        self::assertSame($expected, $getModulePathCacheFilePath);
    }
}
