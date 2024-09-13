<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Domain\Admin\Command;

use OxidEsales\EshopCommunity\Application\Model\User;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Exception\InvalidEmailException;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\RuntimeException;
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

    public function testExecuteWithMissingArgument(): void
    {
        $this->expectException(RuntimeException::class);

        $this->getCommandTester()
            ->execute([
                'admin-email' => self::ADMIN_EMAIL,
            ]);
    }

    public function testExecuteWithInvalidAdminEmail(): void
    {
        $this->expectException(InvalidEmailException::class);

        $this->getCommandTester()
            ->execute([
                'admin-email' => 'admin',
                'admin-password' => 'admin',
            ]);
    }

    public function testExecuteWithCompleteArgs(): void
    {
        $exitCode = $this->getCommandTester()
            ->execute([
                'admin-email' => self::ADMIN_EMAIL,
                'admin-password' => 'some-admin-pass',
            ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
        $this->assertUserExists();
    }

    private function getCommandTester(): CommandTester
    {
        return new CommandTester(
            $this->get('console.command_loader')->get('oe:admin:create-user')
        );
    }

    private function assertUserExists(): void
    {
        $this->assertNotFalse((new User())->getIdByUserName(self::ADMIN_EMAIL));
    }
}
