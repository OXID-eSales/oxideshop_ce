<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\DatabaseProvider;

class ConnectionProvider implements ConnectionProviderInterface
{
    public function get(): Connection
    {
        return DatabaseProvider::getDb()->getPublicConnection();
    }
}
