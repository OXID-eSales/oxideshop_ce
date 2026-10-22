<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\CompositeMigrationExecutor;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolver;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationSqlOutputInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\NullOutput;

final class CompositeMigrationExecutorTest extends TestCase
{
    public function testExecutesEveryExecutorWithTheSameOptionsAndOutput(): void
    {
        $options = ['--dry-run' => true];
        $output = new NullOutput();

        $firstExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $firstExecutor->expects($this->once())->method('executeWithOptions')->with($options, $output)->willReturn(0);
        $secondExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $secondExecutor->expects($this->once())->method('executeWithOptions')->with($options, $output)->willReturn(0);
        $exitCodeResolver = $this->createStub(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver->method('combine')->willReturn(0);

        $executor = new CompositeMigrationExecutor(
            [$firstExecutor, $secondExecutor],
            $exitCodeResolver,
            $this->createStub(MigrationSqlOutputInterface::class),
        );

        $this->assertSame(0, $executor->executeWithOptions($options, $output));
    }

    public function testExecutesExecutorsInConfiguredOrder(): void
    {
        $executed = [];
        $firstExecutor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
        $firstExecutor->method('executeWithOptions')->willReturnCallback(function () use (&$executed): int {
            $executed[] = 'first';

            return 0;
        });
        $secondExecutor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
        $secondExecutor->method('executeWithOptions')->willReturnCallback(function () use (&$executed): int {
            $executed[] = 'second';

            return 0;
        });
        $exitCodeResolver = $this->createStub(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver->method('combine')->willReturn(0);

        (new CompositeMigrationExecutor(
            [$firstExecutor, $secondExecutor],
            $exitCodeResolver,
            $this->createStub(MigrationSqlOutputInterface::class),
        ))->executeWithOptions();

        $this->assertSame(['first', 'second'], $executed);
    }

    public function testCombinesEachExitCodeWithThePreviousResult(): void
    {
        $firstExecutor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
        $firstExecutor->method('executeWithOptions')->willReturn(0);
        $secondExecutor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
        $secondExecutor->method('executeWithOptions')->willReturn(3);
        $thirdExecutor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
        $thirdExecutor->method('executeWithOptions')->willReturn(0);
        $exitCodeResolver = $this->createMock(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver->expects($this->exactly(3))->method('combine')->willReturnMap([
            [0, 0, 0],
            [0, 3, 3],
            [3, 0, 3],
        ]);

        $executor = new CompositeMigrationExecutor(
            [$firstExecutor, $secondExecutor, $thirdExecutor],
            $exitCodeResolver,
            $this->createStub(MigrationSqlOutputInterface::class),
        );

        $this->assertSame(3, $executor->executeWithOptions());
    }

    public function testReturnsSuccessWithoutExecutors(): void
    {
        $exitCodeResolver = $this->createMock(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver->expects($this->never())->method('combine');

        $this->assertSame(
            0,
            (new CompositeMigrationExecutor([], $exitCodeResolver, $this->createStub(MigrationSqlOutputInterface::class)))->executeWithOptions()
        );
    }

    public function testPassesOneSharedSqlFileToAllExecutors(): void
    {
        $sqlOutput = $this->createStub(MigrationSqlOutputInterface::class);
        $sqlOutput->method('resolveFile')->willReturn('/resolved/out.sql');
        $sqlOutput->method('prepare')->willReturn(true);
        $received = [];
        $executors = array_map(function () use (&$received) {
            $executor = $this->createStub(ConfigurableMigrationExecutorInterface::class);
            $executor->method('executeWithOptions')->willReturnCallback(
                function (array $options) use (&$received): int {
                    $received[] = $options['--write-sql'];
                    return 0;
                }
            );
            return $executor;
        }, [1, 2]);

        new CompositeMigrationExecutor($executors, new MigrationExitCodeResolver(), $sqlOutput)
            ->executeWithOptions(['--write-sql' => null]);

        $this->assertSame(['/resolved/out.sql', '/resolved/out.sql'], $received);
    }

    public function testFailsWithoutRunningExecutorsWhenSqlFileCannotBeWritten(): void
    {
        $sqlOutput = $this->createStub(MigrationSqlOutputInterface::class);
        $sqlOutput->method('resolveFile')->willReturn('/resolved/out.sql');
        $sqlOutput->method('prepare')->willReturn(false);
        $executor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $executor->expects($this->never())->method('executeWithOptions');

        $status = new CompositeMigrationExecutor([$executor], new MigrationExitCodeResolver(), $sqlOutput)
            ->executeWithOptions(['--write-sql' => null], new NullOutput());

        $this->assertSame(1, $status);
    }
}
