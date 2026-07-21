<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

interface ShopRunnerInterface
{
    public function run(callable $fallbackController): void;

    public function runController(callable $controller): void;
}
