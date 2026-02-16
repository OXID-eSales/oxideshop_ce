<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryFilterInterface;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryLogger;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryFilter;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class QueryLoggerTest extends TestCase
{
    public function testLoggingEnabled(): void
    {
        $queryFilter = $this->createStub(QueryFilter::class);

        $queryFilter->method('shouldLogQuery')->willReturn(true);

        $psrLogger = $this->getPsrLoggerMock();
        $psrLogger->expects($this->once())->method('debug');

        $this->runQuery($queryFilter, $psrLogger);
    }

    public function testLoggingDisabled(): void
    {
        $queryFilter = $this->createStub(QueryFilter::class);

        $queryFilter->method('shouldLogQuery')->willReturn(false);

        $psrLogger = $this->getPsrLoggerMock();
        $psrLogger->expects($this->never())->method('debug');

        $this->runQuery($queryFilter, $psrLogger);
    }

    private function getPsrLoggerMock(): LoggerInterface
    {
        return $this->createMock(LoggerInterface::class);
    }

    public function runQuery(QueryFilterInterface $queryFilter, LoggerInterface $psrLogger): void
    {
        $logger = new QueryLogger($queryFilter, new ContextStub(), $psrLogger);

        $logger->startQuery('dummy query', [':id' => 'testId']);
        $logger->stopQuery();
    }
}
