<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\AdminUi\Provider;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class AdminUiPackageDirectoryProvider implements AdminUiDirectoryProviderInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private string $packageName,
        private string $relativePath = '',
    ) {
    }

    public function getDirectory(): string
    {
        return Path::join($this->context->getVendorPath(), $this->packageName, $this->relativePath);
    }
}
