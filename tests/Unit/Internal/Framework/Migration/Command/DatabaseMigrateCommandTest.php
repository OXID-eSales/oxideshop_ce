<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Command\DatabaseMigrateCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationOptionsForwarderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class DatabaseMigrateCommandTest extends TestCase
{
    public function testExecutesWrapperAndTaggedMigrationsOnceAndCombinesExitCodes(): void
    {
        $options = ['--dry-run' => true];

        $optionsForwarder = $this->createStub(MigrationOptionsForwarderInterface::class);
        $optionsForwarder->method('collect')->willReturn($options);

        $wrapperExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $wrapperExecutor
            ->expects($this->once())
            ->method('executeWithOptions')
            ->with($options)
            ->willReturn(0);

        $taggedExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $taggedExecutor
            ->expects($this->once())
            ->method('executeWithOptions')
            ->with($options)
            ->willReturn(3);

        $exitCodeResolver = $this->createMock(MigrationExitCodeResolverInterface::class);
        $exitCodeResolver
            ->expects($this->once())
            ->method('combine')
            ->with(0, 3)
            ->willReturn(3);

        $command = new DatabaseMigrateCommand(
            $wrapperExecutor,
            $taggedExecutor,
            $optionsForwarder,
            $exitCodeResolver
        );

        $this->assertSame(3, (new CommandTester($command))->execute([]));
    }
}
