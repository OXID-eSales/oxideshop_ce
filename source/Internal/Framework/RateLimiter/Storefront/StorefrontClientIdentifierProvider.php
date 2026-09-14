<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\Storefront;

use OxidEsales\EshopCommunity\Internal\Framework\RateLimiter\ClientIdentifierProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Request;

readonly class StorefrontClientIdentifierProvider implements ClientIdentifierProviderInterface
{
    public function __construct(private SessionInterface $session)
    {
    }

    public function getClientIdentifier(Request $request): string
    {
        return ((string) $this->session->get('usr', '')) ?: ($request->getClientIp() ?? 'unknown');
    }
}
