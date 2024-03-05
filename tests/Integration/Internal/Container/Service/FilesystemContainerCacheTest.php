<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Container\Service;

use ProjectServiceContainer;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Container\ContainerBuilderFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class FilesystemContainerCacheTest extends TestCase
{
    use ContainerTrait;

    protected function setUp(): void
    {
        $this->get(ContainerCacheInterface::class)->invalidate(2);
        parent::setUp();
    }

    public function testExists(): void
    {
        $cache = $this->get(ContainerCacheInterface::class);
        $cache->put($this->getContainer(), 2);

        $this->assertTrue(
            $cache->exists(2)
        );
    }

    public function testGet(): void
    {
        $cache = $this->get(ContainerCacheInterface::class);
        $cache->put($this->getContainer(), 2);

        $this->assertInstanceOf(
            ProjectServiceContainer::class,
            $cache->get(2)
        );
    }

    public function testInvalidate(): void
    {
        $cache = $this->get(ContainerCacheInterface::class);
        $cache->put($this->getContainer(), 2);

        $cache->invalidate(2);

        $this->assertFalse(
            $cache->exists(2)
        );
    }

    private function getContainer(): ContainerBuilder
    {
        $containerBuilder = (new ContainerBuilderFactory())->create();
        $symfonyContainer = $containerBuilder->getContainer();
        $symfonyContainer->compile();
        return $symfonyContainer;
    }
}
