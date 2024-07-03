<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\DIContainer;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\ContainerBuilder;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;

final class ContainerBuilderTest extends TestCase
{
    public function testContainerParametersAreSet(): void
    {
        $sourcePath = uniqid('source-path-', true);
        $cachePath = uniqid('cache-path-', true);
        $contextStub = new BasicContextStub();
        $contextStub->setSourcePath($sourcePath);
        $contextStub->setCacheDirectory($cachePath);
        $containerBuilder = new ContainerBuilder($contextStub);
        $symfonyContainerBuilder = $containerBuilder->getContainer();

        $this->assertEquals(
            $contextStub->getDefaultShopId(),
            $symfonyContainerBuilder->getParameter('oxid_esales.current_shop_id')
        );
        $this->assertEquals(
            $sourcePath,
            $symfonyContainerBuilder->getParameter('oxid_shop_source_directory')
        );
        $this->assertEquals(
            $cachePath,
            $symfonyContainerBuilder->getParameter('oxid_cache_directory')
        );
    }
}
