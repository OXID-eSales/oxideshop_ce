<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryLogger;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryFilter;
use Psr\Log\LoggerInterface;
use OxidEsales\Eshop\Core\Registry;

class QueryLoggerTest extends \PHPUnit\Framework\TestCase
{
    public function providerTestLogging()
    {
        $data = [
             [
                 'query_pass' => true,
                 'expected'   => 'once'
             ],
             [
                 'query_pass' => false,
                 'expected'   => 'never'
             ]
        ];

        return $data;
    }

    /**
     * @param bool   $queryPass
     * @param string $expected
     *
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
     * Test helper.
     *
     * @param bool $pass
     *
     * @return \PHPUnit\Framework\MockObject\MockObject|QueryFilter
     */
    private function getQueryFilterMock($pass = true)
    {
        $queryFilter = $this->getMockBuilder(QueryFilter::class)
            ->setMethods(['shouldLogQuery'])
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
            ->setMethods(
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
