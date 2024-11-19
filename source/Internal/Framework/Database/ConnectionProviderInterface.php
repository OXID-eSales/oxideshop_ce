<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Connection;

/**
 * @deprecated will be removed in next major, use ConnectionFactoryInterface instead
 */
interface ConnectionProviderInterface
{
    public function get(): Connection;
}
