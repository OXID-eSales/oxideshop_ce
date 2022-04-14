<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use PDO;
use Doctrine\DBAL\Query\QueryBuilder;

class QueryBuilderFactory implements QueryBuilderFactoryInterface
{
    public function __construct(private ConnectionProviderInterface $connectionProvider)
    {
    }

    /**
     * Method returns query builder.
     *
     * @return QueryBuilder
     */
    public function create(): QueryBuilder
    {
        $connection = $this->connectionProvider->get();
        $connection->setFetchMode(PDO::FETCH_ASSOC);

        return $connection->createQueryBuilder();
    }
}
