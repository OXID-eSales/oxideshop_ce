<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\HeaderGenerator\Bridge;

interface HeaderGeneratorBridgeInterface
{
    public function generate(string $filename): void;
}
