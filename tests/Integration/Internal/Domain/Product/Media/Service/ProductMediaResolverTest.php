<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media\Service;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\Service\ProductMediaPathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ProductMediaResolverTest extends TestCase
{
    use ContainerTrait;

    public function testResolveBuildsExpectedPath(): void
    {
        $mediaFile = 'media.jpg';

        $path = (string) $this->get(ProductMediaPathResolverInterface::class)->resolve('product123', $mediaFile);

        $this->assertStringStartsWith(
            Path::join('out', 'pictures', 'media', 'products', 'product123'),
            $path
        );
        $this->assertStringEndsWith($mediaFile, $path);
    }
}
