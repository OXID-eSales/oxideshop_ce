<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use Symfony\Component\HttpFoundation\Request;

interface ThrottleKeyProviderInterface
{
    /**
     * @param array<string, mixed> $rule
     */
    public function keyFor(array $rule, Request $request): string;
}
