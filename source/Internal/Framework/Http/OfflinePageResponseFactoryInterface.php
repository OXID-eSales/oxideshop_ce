<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Http;

use Symfony\Component\HttpFoundation\Response;

interface OfflinePageResponseFactoryInterface
{
    public function create(): Response;
}
