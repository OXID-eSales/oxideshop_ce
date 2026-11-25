<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter;

use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated
 */
interface ClientIdentifierProviderInterface
{
    public function getClientIdentifier(Request $request): string;
}
