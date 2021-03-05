<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\FileSystem\FileGenerator\Bridge;

interface FileGeneratorBridgeInterface
{
    public function generate(string $filename, array $data): void;
}
