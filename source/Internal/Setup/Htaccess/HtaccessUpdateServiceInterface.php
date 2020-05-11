<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

interface HtaccessUpdateServiceInterface
{
    /** @param string $url */
    public function updateRewriteBaseDirective(string $url): void;
}
