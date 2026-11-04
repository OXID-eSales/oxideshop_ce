<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\AdminUi;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class AdminUiPathProvider implements AdminUiPathProviderInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private string $packageDirectory,
    ) {
    }

    public function getAbsolutePath(): string
    {
        return Path::join($this->context->getVendorPath(), $this->packageDirectory);
    }
}
