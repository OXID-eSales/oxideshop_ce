<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter;

use Symfony\Component\RateLimiter\LimiterInterface;

interface ApiRateLimiterFactoryInterface
{
    public function create(string $clientIdentifier): LimiterInterface;
}
