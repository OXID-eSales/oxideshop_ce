<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Driver\Connection;
use OxidEsales\Eshop\Core\DatabaseProvider;

class ConnectionFactory implements ConnectionFactoryInterface
{
    public function create(): Connection
    {
        return DatabaseProvider::getDb()->getPublicConnection();
    }
}
