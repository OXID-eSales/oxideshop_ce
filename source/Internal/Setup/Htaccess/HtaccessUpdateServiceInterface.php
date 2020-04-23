<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

interface HtaccessUpdateServiceInterface
{
    /** @param string $rewriteBase */
    public function updateRewriteBaseDirective(string $rewriteBase): void;
}
