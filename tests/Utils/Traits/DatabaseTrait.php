<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Utils\Traits;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Facts\Facts;

trait DatabaseTrait
{
    public function beginTransaction()
    {
        DatabaseProvider::getDb()->startTransaction();
    }

    public function rollBackTransaction()
    {
        if (DatabaseProvider::getDb()->isTransactionActive()) {
            DatabaseProvider::getDb()->rollbackTransaction();
        }
    }

    /**
     * @param string $query
     */
    public function executeSqlQuery(string $query): void
    {
        DatabaseProvider::getDb()->execute($query);
    }

    public function setupShopDatabase()
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