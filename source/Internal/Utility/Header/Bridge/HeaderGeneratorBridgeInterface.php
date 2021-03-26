<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Utility\Header\Bridge;

interface HeaderGeneratorBridgeInterface
{
    /**
     * @param string $filename
     */
    public function generate(string $filename): void;
}
