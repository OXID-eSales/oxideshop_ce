<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use Doctrine\DBAL\Query\QueryBuilder;
use PDO;

class QueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(
        private readonly ConnectionFactoryInterface $connectionFactory
    ) {
    }

    public function create(): QueryBuilder
    {
        $connection = $this->connectionFactory->create();
        $connection->setFetchMode(PDO::FETCH_ASSOC);

        return new QueryBuilder($connection);
    }
}
