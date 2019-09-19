<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database\Logger;

use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\DatabaseLoggerFactory;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\NullLogger;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Logger\QueryLogger;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\ContextStub;

class DatabaseLoggerFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreationForAdminLogEnabled()
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

    public function testCreationForAdminLogDisabled()
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

    public function testCreationForNormalUser()
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
