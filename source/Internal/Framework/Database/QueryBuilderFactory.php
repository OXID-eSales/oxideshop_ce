<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Database;

use PDO;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;

class QueryBuilderFactory implements QueryBuilderFactoryInterface
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
     * Method returns query builder.
     *
     * @return QueryBuilder
     */
    public function create(): QueryBuilder
    {
        $this->connection->setFetchMode(PDO::FETCH_ASSOC);

        return $this->connection->createQueryBuilder();
    }
}
