<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Request;

/**
 * @deprecated since v8.0.0. Use Symfony\Component\HttpFoundation\Request instead.
 */
interface RequestInterface
{
    public function get(string $key, mixed $default = null): mixed;
}
