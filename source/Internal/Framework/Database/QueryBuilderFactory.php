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
    /**
     * @var ConnectionProviderInterface
     */
    private $connectionProvider;

    public function __construct(ConnectionProviderInterface $connectionProvider)
    {
        $this->connectionProvider = $connectionProvider;
    }

    /**
     * Method returns query builder.
     */
    public function create(): QueryBuilder
    {
        $connection = $this->connectionProvider->get();
        $connection->setFetchMode(PDO::FETCH_ASSOC);

        return $connection->createQueryBuilder();
    }
}
