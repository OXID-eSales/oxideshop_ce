<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Logger;

/**
 * @internal
 */
interface QueryFilterInterface
{
    /**
     * @param string $query Query string
     * @param array $skipLogTags Additional tags to skip
     */
    public function shouldLogQuery(string $query, array $skipLogTags): bool;
}
