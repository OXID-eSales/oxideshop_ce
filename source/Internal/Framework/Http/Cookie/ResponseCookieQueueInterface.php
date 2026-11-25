<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Http\Cookie;

use Symfony\Component\HttpFoundation\Cookie;

interface ResponseCookieQueueInterface
{
    public function add(Cookie $cookie): void;

    /**
     * @return list<Cookie>
     */
    public function getAll(): array;

    public function clear(): void;
}
