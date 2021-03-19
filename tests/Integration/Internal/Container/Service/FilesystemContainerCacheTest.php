<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Integration\Internal\Container\Service;

use OxidEsales\EshopCommunity\Internal\Container\ContainerBuilderFactory;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class FilesystemContainerCacheTest extends TestCase
{
    use ContainerTrait;

    protected function setUp(): void
    {
        $this->get(ContainerCacheInterface::class)->invalidate();
        parent::setUp();
    }

    public function testExists(): void
    {
        $cache = $this->get(ContainerCacheInterface::class);
        $cache->put($this->getContainer());

        $this->assertTrue(
            $cache->exists()
        );
    }

    public function testGet(): void
    {
        $cache = $this->get(ContainerCacheInterface::class);
        $cache->put($this->getContainer());

        $this->assertInstanceOf(
            \ProjectServiceContainer::class,
            $cache->get()
        );
    }

    public function testInvalidate(): void
    {
        $cache = $this->get(ContainerCacheInterface::class);
        $cache->put($this->getContainer());

        $cache->invalidate();

        $this->assertFalse(
            $cache->exists()
        );
    }

    private function getContainer(): \Symfony\Component\DependencyInjection\ContainerBuilder
    {
        $containerBuilder = (new ContainerBuilderFactory())->create();
        $symfonyContainer = $containerBuilder->getContainer();
        $symfonyContainer->compile();
        return $symfonyContainer;
    }
}
