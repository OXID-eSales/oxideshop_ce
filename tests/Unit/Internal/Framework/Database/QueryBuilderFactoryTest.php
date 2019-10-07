<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactory;
use PDO;

class QueryBuilderFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testQueryBuilderCreation()
    {
        $connection = $this
            ->getMockBuilder(Connection::class)
            ->setMethods(null)
            ->disableOriginalConstructor()
            ->getMock();

        $queryBuilderFactory = new QueryBuilderFactory($connection);

        $this->assertInstanceOf(
            QueryBuilder::class,
            $queryBuilderFactory->create()
        );
    }

    public function testFetchMode()
    {
        $connection = $this
            ->getMockBuilder(Connection::class)
            ->setMethods(['setFetchMode'])
            ->disableOriginalConstructor()
            ->getMock();

        $connection
            ->expects($this->once())
            ->method('setFetchMode')
            ->with(
                $this->equalTo(PDO::FETCH_ASSOC)
            );

        $queryBuilderFactory = new QueryBuilderFactory($connection);
        $queryBuilderFactory->create();
    }
}
