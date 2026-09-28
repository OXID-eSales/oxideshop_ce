<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront\Service;

interface RuleProviderInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function getMatchingRules(): array;
}
