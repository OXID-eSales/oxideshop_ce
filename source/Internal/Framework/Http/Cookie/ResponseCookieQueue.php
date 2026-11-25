<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

class ResponseCookieQueue implements ResponseCookieQueueInterface
{
    private array $cookies = [];

    public function add(Cookie $cookie): void
    {
        $this->cookies[] = $cookie;
    }

    public function getAll(): array
    {
        return $this->cookies;
    }

    public function clear(): void
    {
        $this->cookies = [];
    }
}
