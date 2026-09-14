<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use Symfony\Component\RateLimiter\LimiterInterface;

interface LimiterProviderInterface
{
    /**
     * @param array<string, mixed> $rule
     */
    public function limiterFor(array $rule, string $key): LimiterInterface;
}
