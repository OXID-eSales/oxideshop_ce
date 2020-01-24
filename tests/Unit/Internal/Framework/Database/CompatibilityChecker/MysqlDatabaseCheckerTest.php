<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Database\CompatibilityChecker;

use OxidEsales\EshopCommunity\Internal\Framework\Database\CompatibilityChecker\DatabaseVersionDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Database\CompatibilityChecker\MysqlDatabaseChecker;
use PHPUnit\Framework\TestCase;

final class MysqlDatabaseCheckerTest extends TestCase
{
    public function testIsCompatibleReturnsTrueWithCorrectVersion(): void
    {
        $databaseVersionDao = $this->prophesize(DatabaseVersionDaoInterface::class);
        $databaseVersionDao
            ->getVersion()
            ->willReturn('5.7');

        $databaseChecker = new MysqlDatabaseChecker($databaseVersionDao->reveal());

        $this->assertTrue(
            $databaseChecker->isDatabaseCompatible()
        );
    }

    public function testIsCompatibleReturnsFalseWithIncorrectVersion(): void
    {
        $databaseVersionDao = $this->prophesize(DatabaseVersionDaoInterface::class);
        $databaseVersionDao
            ->getVersion()
            ->willReturn('5.5');

        $databaseChecker = new MysqlDatabaseChecker($databaseVersionDao->reveal());

        $this->assertFalse(
            $databaseChecker->isDatabaseCompatible()
        );
    }

    public function testCompatibilityNoticesWithNotRecommendedVersion(): void
    {
        $databaseVersionDao = $this->prophesize(DatabaseVersionDaoInterface::class);
        $databaseVersionDao
            ->getVersion()
            ->willReturn('5.6');

        $databaseChecker = new MysqlDatabaseChecker($databaseVersionDao->reveal());

        $this->assertContains(
            'ERROR_MYSQL_56_NOT_RECOMMENDED',
            $databaseChecker->getCompatibilityNotices()
        );
    }

    public function testCompatibilityNoticesWithCorrectVersion(): void
    {
        $databaseVersionDao = $this->prophesize(DatabaseVersionDaoInterface::class);
        $databaseVersionDao
            ->getVersion()
            ->willReturn('8.0');

        $databaseChecker = new MysqlDatabaseChecker($databaseVersionDao->reveal());

        $this->assertSame(
            [],
            $databaseChecker->getCompatibilityNotices()
        );
    }
}
