<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Request;

/**
 * @deprecated will be removed in v8.0.
 */
interface HttpsRequestResolverInterface
{
    public function isHttps(): bool;
}
