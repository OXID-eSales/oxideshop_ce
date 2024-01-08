<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryLogger;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryFilter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class QueryLoggerTest extends TestCase
{
    public static function providerTestLogging(): array
    {
        return [
             [
                 'query_pass' => true,
                 'expected'   => 'once'
             ],
             [
                 'query_pass' => false,
                 'expected'   => 'never'
             ]
        ];
    }

    /**
     * @dataProvider providerTestLogging
     */
    public function testLogging(bool $queryPass, string $expected)
    {
        $context = new ContextStub();

        $queryFilter = $this->getQueryFilterMock($queryPass);
        $psrLogger = $this->getPsrLoggerMock();

        $psrLogger->expects($this->$expected())
            ->method('debug');

        $logger = new QueryLogger($queryFilter, $context, $psrLogger);
        $query = 'dummy test query where oxid = :id ';

        $logger->startQuery($query, [':id' => 'testid']);
        $logger->stopQuery();
    }

    /**
     * @return \PHPUnit\Framework\MockObject\MockObject|QueryFilter
     */
    private function getQueryFilterMock($pass = true)
    {
        $queryFilter = $this->getMockBuilder(QueryFilter::class)
            ->onlyMethods(['shouldLogQuery'])
            ->getMock();

        $queryFilter->expects($this->any())
            ->method('shouldLogQuery')
            ->willReturn($pass);

        return $queryFilter;
    }

    /**
     * Test helper.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|LoggerInterface
     */
    private function getPsrLoggerMock()
    {
        $psrLogger = $this->getMockBuilder(LoggerInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(
                [
                    'emergency',
                    'alert',
                    'critical',
                    'error',
                    'warning',
                    'notice',
                    'info',
                    'debug',
                    'log'
                ]
            )
            ->getMock();

        return $psrLogger;
    }
}
