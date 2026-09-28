<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

use Symfony\Component\HttpFoundation\Request;

interface KeyProviderInterface
{
    /**
     * @param array<string, mixed> $rule
     */
    public function get(array $rule, Request $request): string;
}
