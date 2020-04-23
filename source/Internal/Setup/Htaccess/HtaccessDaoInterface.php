<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Setup\Htaccess;

interface HtaccessDaoInterface
{
    /**
     * @param string $rewriteBase
     */
    public function setRewriteBase(string $rewriteBase): void;
}
