<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\CompatibilityChecker;

interface DatabaseVersionDaoInterface
{
    /** @return string */
    public function getVersion(): string;
}
