<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Migration\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Command\DatabaseMigrateCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationOptionsForwarderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Tester\CommandTester;

final class DatabaseMigrateCommandTest extends TestCase
{
    public function testForwardsCollectedOptionsToTheExecutorAndReturnsItsExitCode(): void
    {
        $options = ['--dry-run' => true];

        $optionsForwarder = $this->createStub(MigrationOptionsForwarderInterface::class);
        $optionsForwarder->method('collect')->willReturn($options);

        $migrationExecutor = $this->createMock(ConfigurableMigrationExecutorInterface::class);
        $migrationExecutor
            ->expects($this->once())
            ->method('executeWithOptions')
            ->with($options, $this->isInstanceOf(OutputInterface::class))
            ->willReturn(3);

        $command = new DatabaseMigrateCommand($migrationExecutor, $optionsForwarder);

        $this->assertSame(3, (new CommandTester($command))->execute([]));
    }
}
