<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use Symfony\Component\HttpFoundation\Request;

interface ShopRunnerInterface
{
    public function run(Request $request, callable $fallbackController): void;

    public function runRoutes(Request $request): void;

    public function runController(Request $request, callable $controller): void;
}
