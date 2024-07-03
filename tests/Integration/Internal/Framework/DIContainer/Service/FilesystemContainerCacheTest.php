<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\DIContainer\Service;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Service\ContainerCacheInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;

final class FilesystemContainerCacheTest extends TestCase
{
    use ContainerTrait;

    public function tearDown(): void
    {
        $this->get(ContainerCacheInterface::class)->invalidate(2);
    }

    public function testContainerCacheInvalidation(): void
    {
        $containerCache = $this->get(ContainerCacheInterface::class);

        $this->assertTrue($containerCache->exists(2));
        $containerCache->invalidate(2);
        $this->assertFalse($containerCache->exists(2));

        $containerCache->put($this->container, 2);
        $this->assertTrue($containerCache->exists(2));
    }
}
