<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Common\Database;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactory;
use PDO;

class QueryBuilderFactoryTest extends \PHPUnit_Framework_TestCase
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
