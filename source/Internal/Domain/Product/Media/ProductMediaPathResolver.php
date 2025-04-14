<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Domain\Product\Media;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ProductMediaPathResolver implements ProductMediaPathResolverInterface
{
    public function __construct(
        private ContextInterface $context
    ) {
    }

    public function getRelativePath(string $filename): string
    {
        return Path::join(
            Path::makeRelative(
                $this->context->getOutPath(),
                $this->context->getSourcePath()
            ),
            'pictures',
            'media',
            $filename
        );
    }
}
