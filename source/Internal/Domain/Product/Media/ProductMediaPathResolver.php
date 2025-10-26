<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Domain\Media\DataObject\MediaPath;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ProductMediaPathResolver implements ProductMediaPathResolverInterface
{
    public function __construct(
        private ContextInterface $context
    ) {
    }

    public function resolve(string $productId, string $filename): MediaPath
    {
        return new MediaPath(
            Path::join(
                Path::makeRelative(
                    $this->context->getOutPath(),
                    $this->context->getSourcePath()
                ),
                'pictures',
                'media',
                'products',
                $productId,
                $filename
            )
        );
    }
}
