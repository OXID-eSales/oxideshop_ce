<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter;

use Symfony\Component\HttpFoundation\Request;

readonly class ClientIdentifierProvider implements ClientIdentifierProviderInterface
{
    public function getClientIdentifier(Request $request): string
    {
        return $request->getClientIp() ?? 'unknown';
    }
}
