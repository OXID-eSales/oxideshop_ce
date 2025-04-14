<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Product\Media\ProductMediaPathResolverInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Filesystem\Path;

final class ProductMediaResolverTest extends TestCase
{
    use ContainerTrait;

    public function testGetRelativePath(): void
    {
        $mediaFile = 'media.jpg';

        $relativePath = $this->get(ProductMediaPathResolverInterface::class)->getRelativePath($mediaFile);

        $this->assertStringStartsWith(
            basename($this->get(ContextInterface::class)->getOutPath()),
            $relativePath
        );
        $this->assertStringEndsWith($mediaFile, $relativePath);
        $this->assertTrue(Path::isRelative($relativePath));
    }
}
