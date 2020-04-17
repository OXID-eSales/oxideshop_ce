<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\Database\Adapter\Doctrine\Database;
use OxidEsales\Eshop\Core\DatabaseProvider;

class ConnectionProvider implements ConnectionProviderInterface
{
    private $connection;

    public function get(): Connection
    {
        if ($this->connection === null) {
            $database = DatabaseProvider::getDb();
            $r = new \ReflectionMethod(Database::class, 'getConnection');
            $r->setAccessible(true);

            $this->connection = $r->invoke($database);
        }

        return $this->connection;
    }
}
