<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

interface HtaccessUpdaterInterface
{
    /** @param string $url */
    public function updateRewriteBaseDirective(ShopBaseUrl $shopBaseUrl): void;
}
