<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

/**
 * @deprecated will be removed in next major, use methods from \Doctrine\DBAL\Driver\Connection directly
 */
interface TransactionServiceInterface
{
    /**
     * Initiates a transaction.
     */
    public function begin();

    /**
     * Commits a transaction.
     */
    public function commit();

    /**
     * Rolls back the current transaction.
     */
    public function rollback();
}
