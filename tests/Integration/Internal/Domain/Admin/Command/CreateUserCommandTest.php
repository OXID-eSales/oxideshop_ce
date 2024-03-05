<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Admin\Command;

use PHPUnit\Framework\Attributes\DataProvider;
use InvalidArgumentException;
use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidEmailException;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

final class CreateUserCommandTest extends TestCase
{
    use ContainerTrait;

    private const ADMIN_EMAIL = 'someone@test.com';

    public function tearDown(): void
    {
        $user = new User();
        $user->delete($user->getIdByUserName(self::ADMIN_EMAIL));

        parent::tearDown();
    }

    #[DataProvider('missingOptionsDataProvider')]
    public function testExecuteWithMissingArgs(string $command): void
    {
        $options = [
            '--admin-password' => 'some-admin-pass',
            '--admin-email'    => self::ADMIN_EMAIL,
        ];

        $this->expectException(InvalidArgumentException::class);

        $commandTester = new CommandTester($this->getCommand());
        unset($options[$command]);
        $commandTester->execute($options);
    }

    public static function missingOptionsDataProvider(): array
    {
        return [
            'Missing admin-email'    => ['--admin-password'],
            'Missing admin-password' => ['--admin-email'],
        ];
    }

    public function testExecuteWithInvalidAdminEmail(): void
    {
        $this->expectException(InvalidEmailException::class);
        $commandTester = new CommandTester($this->getCommand());
        $commandTester->execute([
            '--admin-email'    => 'admin',
            '--admin-password' => 'admin',
        ]);
    }

    public function testExecuteWithCompleteArgs(): void
    {
        $commandTester = new CommandTester($this->getCommand());
        $exitCode = $commandTester->execute([
            '--admin-email'    => self::ADMIN_EMAIL,
            '--admin-password' => 'some-admin-pass',
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertUserExists();
    }

    private function getCommand(): Command
    {
        return $this->get('console.command_loader')->get('oe:admin:create-user');
    }

    private function assertUserExists(): void
    {
        $this->assertNotFalse((new User())->getIdByUserName(self::ADMIN_EMAIL));
    }
}
