<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlOutputException;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlOutputInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\SqlCollectingMigrationExecutor;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

final class SqlCollectingMigrationExecutorTest extends TestCase
{
    public function testRunsExecutorWithinSqlCollection(): void
    {
        $sqlOutput = $this->createStub(MigrationSqlOutputInterface::class);
        $sqlOutput->method('collect')->willReturnCallback(
            static fn(array $options, callable $migrations): int => $migrations(['--write-sql' => '/resolved.sql'])
        );
        $executor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $executor->expects($this->once())
            ->method('executeWithOptions')
            ->with(['--write-sql' => '/resolved.sql'])
            ->willReturn(3);

        $this->assertSame(
            3,
            new SqlCollectingMigrationExecutor($executor, $sqlOutput)->executeWithOptions(['--write-sql' => null])
        );
    }

    public function testReportsFailureWhenSqlCannotBeWritten(): void
    {
        $sqlOutput = $this->createStub(MigrationSqlOutputInterface::class);
        $sqlOutput->method('collect')->willThrowException(new MigrationSqlOutputException());
        $output = new BufferedOutput();

        $status = new SqlCollectingMigrationExecutor(
            $this->createStub(ConfigurableMigrationExecutorInterface::class),
            $sqlOutput
        )->executeWithOptions(['--write-sql' => null], $output);

        $this->assertSame(1, $status);
        $this->assertNotEmpty($output->fetch());
    }
}
