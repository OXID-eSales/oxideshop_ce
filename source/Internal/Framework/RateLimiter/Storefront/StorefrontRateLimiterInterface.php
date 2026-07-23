<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront;

use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

interface StorefrontRateLimiterInterface
{
    /**
     * @throws TooManyRequestsHttpException
     */
    public function enforce(): void;
}
