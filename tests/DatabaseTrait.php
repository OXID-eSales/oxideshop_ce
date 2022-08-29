<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests;

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
