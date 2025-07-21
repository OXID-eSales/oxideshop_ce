<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Request;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Request\RequestInterface;

class RequestAdapter implements RequestInterface
{
    public function get(string $key, mixed $default = null): mixed
    {
        return Registry::getRequest()->getRequestParameter($key, $default);
    }
}
