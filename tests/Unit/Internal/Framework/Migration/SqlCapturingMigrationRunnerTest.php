<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationRunnerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlOutputInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\SqlCapturingMigrationRunner;
use PHPUnit\Framework\TestCase;

final class SqlCapturingMigrationRunnerTest extends TestCase
{
    public function testRunsMigrationWithinSqlCapture(): void
    {
        $sqlOutput = $this->createStub(MigrationSqlOutputInterface::class);
        $sqlOutput->method('capture')->willReturnCallback(
            static fn(array $options, callable $migration): int => $migration(['--write-sql' => '/temporary.sql'])
        );
        $runner = $this->createMock(MigrationRunnerInterface::class);
        $runner->expects($this->once())
            ->method('run')
            ->with('config.yml', ['--write-sql' => '/temporary.sql'])
            ->willReturn(3);

        $this->assertSame(
            3,
            new SqlCapturingMigrationRunner($runner, $sqlOutput)->run('config.yml', ['--write-sql' => '/out.sql'])
        );
    }
}
