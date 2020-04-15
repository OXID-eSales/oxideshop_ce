<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Database;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionProvider;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactory;
use PDO;
use ReflectionClass;

final class QueryBuilderFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testQueryBuilderCreation()
    {
        $connectionProvider = new ConnectionProvider();

        $queryBuilderFactory = new QueryBuilderFactory($connectionProvider);

        $this->assertInstanceOf(
            QueryBuilder::class,
            $queryBuilderFactory->create()
        );
    }

    public function testFetchModeIsSetToAssoc()
    {
        $connectionProvider = new ConnectionProvider();

        $queryBuilderFactory = new QueryBuilderFactory($connectionProvider);
        $queryBuilder = $queryBuilderFactory->create();

        $connection = $queryBuilder->getConnection();

        $reflectionClassConnection = new ReflectionClass($connection);
        $fetchMode = $reflectionClassConnection->getProperty('defaultFetchMode');
        $fetchMode->setAccessible(true);
        $this->assertSame($fetchMode->getValue($connection), PDO::FETCH_ASSOC);
    }
}
