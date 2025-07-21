<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Request;

interface RequestInterface
{
    public function get(string $key, mixed $default = null): mixed;
}
