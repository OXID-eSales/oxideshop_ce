<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Driver\Connection;

interface ConnectionFactoryInterface
{
    public function create(): Connection;
}
