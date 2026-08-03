<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Request;

use Symfony\Component\HttpFoundation\Request;

class RequestFactory
{
    public function __construct(private readonly array $trustedProxies)
    {
    }

    public function create(): Request
    {
        Request::setTrustedProxies(
            $this->trustedProxies,
            Request::HEADER_X_FORWARDED_FOR | Request::HEADER_X_FORWARDED_PROTO
        );
        return Request::createFromGlobals();
    }
}
