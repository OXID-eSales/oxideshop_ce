<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use Symfony\Component\HttpFoundation\Request;

interface RuleProviderInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function rulesFor(Request $request): array;
}
