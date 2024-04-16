<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

use Doctrine\DBAL\Connection;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Facts\Facts;

trait DatabaseTrait
{
    public function beginTransaction(Connection $connection = null): void
    {
        $connection ??= $this->getDbConnection();
        $connection->beginTransaction();
    }

    public function rollBackTransaction(Connection $connection = null): void
    {
        $connection ??= $this->getDbConnection();
        if ($connection->isTransactionActive()) {
            $connection->rollBack();
        }
    }

    public function getDbConnection(): Connection
    {
        return DatabaseProvider::getDb()->getPublicConnection();
    }

    public function setupShopDatabase(): void
    {
        $facts = new Facts();
        exec(
            $facts->getCommunityEditionRootPath() .
            '/bin/oe-console oe:database:reset' .
            ' --db-host=' . $facts->getDatabaseHost() .
            ' --db-port=' . $facts->getDatabasePort() .
            ' --db-name=' . $facts->getDatabaseName() .
            ' --db-user=' . $facts->getDatabaseUserName() .
            ' --db-password=' . $facts->getDatabasePassword() .
            ' --force'
        );
    }
}
