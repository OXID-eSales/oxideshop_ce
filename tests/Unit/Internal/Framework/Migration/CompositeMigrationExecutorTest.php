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

        $executor = new CompositeMigrationExecutor([$firstExecutor, $secondExecutor], $exitCodeResolver);

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

        (new CompositeMigrationExecutor([$firstExecutor, $secondExecutor], $exitCodeResolver))->executeWithOptions();

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

        $executor = new CompositeMigrationExecutor([$firstExecutor, $secondExecutor, $thirdExecutor], $exitCodeResolver);

        $this->assertSame(3, $executor->executeWithOptions());
    }

    public function testReturnsSuccessWithoutExecutors(): void
    {
        $exitCodeResolver = $this->createMock(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver->expects($this->never())->method('combine');

        $this->assertSame(0, (new CompositeMigrationExecutor([], $exitCodeResolver))->executeWithOptions());
    }
}
