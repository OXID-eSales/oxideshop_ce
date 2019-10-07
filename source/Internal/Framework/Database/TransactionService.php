<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Connection;

class TransactionService implements TransactionServiceInterface
{
    /**
     * @var Connection
     */
    private $connection;

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Initiates a transaction.
     */
    public function begin()
    {
        $this->connection->beginTransaction();
    }

    /**
     * Commits a transaction.
     */
    public function commit()
    {
        $this->connection->commit();
    }

    /**
     * Rolls back the current transaction.
     */
    public function rollback()
    {
        $this->connection->rollBack();
    }
}
