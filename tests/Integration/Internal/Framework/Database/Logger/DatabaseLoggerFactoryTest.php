<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\DatabaseLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\NullLogger;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryLogger;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;
use PHPUnit\Framework\TestCase;

final class DatabaseLoggerFactoryTest extends TestCase
{
    public function testCreationForAdminLogEnabled(): void
    {
        $context = new ContextStub();
        $context->setIsAdmin(true);
        $context->setIsEnabledAdminQueryLog(true);

        $loggerFactory = new DatabaseLoggerFactory(
            $context,
            $this->getMockBuilder(QueryLogger::class)
                ->disableOriginalConstructor()
                ->getMock(),
            new NullLogger()
        );

        $this->assertInstanceOf(
            QueryLogger::class,
            $loggerFactory->getDatabaseLogger()
        );
    }

    public function testCreationForAdminLogDisabled(): void
    {
        $context = new ContextStub();
        $context->setIsAdmin(true);
        $context->setIsEnabledAdminQueryLog(false);

        $loggerFactory = new DatabaseLoggerFactory(
            $context,
            $this->getMockBuilder(QueryLogger::class)
                ->disableOriginalConstructor()
                ->getMock(),
            new NullLogger()
        );

        $this->assertInstanceOf(
            NullLogger::class,
            $loggerFactory->getDatabaseLogger()
        );
    }

    public function testCreationForNormalUser(): void
    {
        $context = new ContextStub();
        $context->setIsAdmin(false);
        $context->setIsEnabledAdminQueryLog(true);

        $loggerFactory = new DatabaseLoggerFactory(
            $context,
            $this->getMockBuilder(QueryLogger::class)
                ->disableOriginalConstructor()
                ->getMock(),
            new NullLogger()
        );

        $this->assertInstanceOf(
            NullLogger::class,
            $loggerFactory->getDatabaseLogger()
        );
    }
}
