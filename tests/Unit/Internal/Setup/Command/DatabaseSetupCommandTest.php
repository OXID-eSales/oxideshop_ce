<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Setup\Command;

use OxidEsales\EshopCommunity\Internal\Setup\Command\DatabaseSetupCommand;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Exception\DatabaseExistsAndNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\Service\DatabaseInstallerInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class DatabaseSetupCommandTest extends TestCase
{
    use ProphecyTrait;

    private const HOST = 'some-host';
    private const PORT = 123;
    private const DB = 'some-db';
    private const DB_USER = 'some-db-user';
    private const DB_PASS = 'some-db-pass';

    private $arguments = [
        '--db-host' => self::HOST,
        '--db-port' => self::PORT,
        '--db-name' => self::DB,
        '--db-user' => self::DB_USER,
        '--db-password' => self::DB_PASS
    ];

    public function testExecuteWithMissingArgs(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $databaseSetupCommand = new DatabaseSetupCommand(
            $this->getDatabaseCheckerMock(),
            $this->getDatabaseInstallerMock()
        );
        $commandTester = new CommandTester($databaseSetupCommand);
        $commandTester->execute([]);
    }

    public function testExecuteWithExistingDatabaseAndWithForceParameter(): void
    {
        $databaseSetupCommand = new DatabaseSetupCommand(
            $this->getDatabaseCheckerWithExceptionMock(),
            $this->getDatabaseInstallerMock()
        );
        $commandTester = new CommandTester($databaseSetupCommand);

        $arguments = $this->arguments;
        $arguments['--force-installation'] = true;
        $exitCode = $commandTester->execute($arguments);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Setup has been finished.', $commandTester->getDisplay());
    }

    public function testExecuteOnEmptyDatabase(): void
    {
        $databaseSetupCommand = new DatabaseSetupCommand(
            $this->getDatabaseCheckerMock(),
            $this->getDatabaseInstallerMock()
        );
        $commandTester = new CommandTester($databaseSetupCommand);
        $exitCode = $commandTester->execute($this->arguments);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Setup has been finished.', $commandTester->getDisplay());
    }

    public function testExecuteWithExistingDatabaseAndConfirmedAction(): void
    {
        $commandTester = new CommandTester($this->getCommandWithInteraction());
        $commandTester->setInputs(['yes']);

        $exitCode = $commandTester->execute($this->arguments);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Setup has been finished.', $commandTester->getDisplay());
    }

    public function testExecuteWithExistingDatabaseAndRejectedAction(): void
    {
        $commandTester = new CommandTester($this->getCommandWithInteraction());
        $commandTester->setInputs(['no']);
        $exitCode = $commandTester->execute($this->arguments);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertStringContainsString('Setup has been canceled.', $commandTester->getDisplay());
    }

    private function getCommandWithInteraction(): Command
    {
        $databaseSetupCommand = new DatabaseSetupCommand(
            $this->getDatabaseCheckerWithExceptionMock(),
            $this->getDatabaseInstallerMock()
        );
        $databaseSetupCommand->setName('oe:setup:database');

        $application = new Application();
        $application->add($databaseSetupCommand);
        return $application->find('oe:setup:database');
    }

    private function getDatabaseCheckerWithExceptionMock(): DatabaseCheckerInterface
    {
        $databaseChecker = $this->prophesize(DatabaseCheckerInterface::class);
        $databaseChecker->canCreateDatabase(
            self::HOST,
            self::PORT,
            self::DB_USER,
            self::DB_PASS,
            self::DB
        )
            ->willThrow(DatabaseExistsAndNotEmptyException::class);
        return $databaseChecker->reveal();
    }

    private function getDatabaseCheckerMock(): DatabaseCheckerInterface
    {
        return $this->prophesize(DatabaseCheckerInterface::class)->reveal();
    }

    private function getDatabaseInstallerMock(): DatabaseInstallerInterface
    {
        return $this->prophesize(DatabaseInstallerInterface::class)->reveal();
    }
}
