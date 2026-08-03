<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Request;

interface HttpsRequestResolverInterface
{
    public function isHttps(): bool;
}
