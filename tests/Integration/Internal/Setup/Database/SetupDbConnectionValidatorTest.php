<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use Doctrine\DBAL\Exception\ConnectionException;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Database\DatabaseNotEmptyException;
use OxidEsales\EshopCommunity\Internal\Setup\Database\SetupDbConnectionValidatorInterface;
use OxidEsales\EshopCommunity\Internal\Setup\Database\UnsupportedDatabaseConfigurationException;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;

final class SetupDbConnectionValidatorTest extends TestCase
{
    use ContainerTrait;
    use ProphecyTrait;

    public function testValidateWithSocketConnection(): void
    {
        $dbConfig = $this->prophesize(DatabaseConfiguration::class);
        $dbConfig->isSocketConnection()->willReturn(true);
        $dbConfig->getDatabaseUrl()->willReturn(Argument::type('string'));

        $this->expectException(UnsupportedDatabaseConfigurationException::class);

        $this->get(SetupDbConnectionValidatorInterface::class)->validate($dbConfig->reveal());
    }

    public function testValidateWithNoUsername(): void
    {
        $dbConfig = $this->prophesize(DatabaseConfiguration::class);
        $dbConfig->isSocketConnection()->willReturn(false);
        $dbConfig->getUser()->willReturn('');
        $dbConfig->getDatabaseUrl()->willReturn(Argument::type('string'));

        $this->expectException(UnsupportedDatabaseConfigurationException::class);

        $this->get(SetupDbConnectionValidatorInterface::class)->validate($dbConfig->reveal());
    }

    public function testValidateWithNoPass(): void
    {
        $dbConfig = $this->prophesize(DatabaseConfiguration::class);
        $dbConfig->isSocketConnection()->willReturn(false);
        $dbConfig->getUser()->willReturn('user');
        $dbConfig->getPass()->willReturn('');
        $dbConfig->getDatabaseUrl()->willReturn(Argument::type('string'));

        $this->expectException(UnsupportedDatabaseConfigurationException::class);

        $this->get(SetupDbConnectionValidatorInterface::class)->validate($dbConfig->reveal());
    }

    public function testValidateWithNoDbName(): void
    {
        $dbConfig = $this->prophesize(DatabaseConfiguration::class);
        $dbConfig->isSocketConnection()->willReturn(false);
        $dbConfig->getUser()->willReturn('user');
        $dbConfig->getPass()->willReturn('pass');
        $dbConfig->getName()->willReturn('');
        $dbConfig->getDatabaseUrl()->willReturn(Argument::type('string'));

        $this->expectException(UnsupportedDatabaseConfigurationException::class);

        $this->get(SetupDbConnectionValidatorInterface::class)->validate($dbConfig->reveal());
    }

    public function testValidateWithNoServerConnection(): void
    {
        $dbConfig = $this->prophesize(DatabaseConfiguration::class);
        $dbConfig->isSocketConnection()->willReturn(false);
        $dbConfig->getUser()->willReturn('user');
        $dbConfig->getPass()->willReturn('pass');
        $dbConfig->getName()->willReturn('db-name');
        $dbConfig->getConnectionParameters()->willReturn([
            'user' => 'user',
            'password' => 'pass',
            'host' => uniqid('db-server-', true),
            'driver' => 'pdo_mysql',
            'port' => 3306,
        ]);

        $this->expectException(ConnectionException::class);

        $this->get(SetupDbConnectionValidatorInterface::class)->validate($dbConfig->reveal());
    }

    #[DoesNotPerformAssertions]
    public function testValidateWithNonExistingDb(): void
    {
        $currentDbConfig = new DatabaseConfiguration(getenv('OXID_DB_URL'));
        $nonExistentDb = uniqid('db-name-', true);
        $configWithNonexistentDatabase = new DatabaseConfiguration(
            sprintf(
                '%s://%s:%s@%s:%s/%s',
                parse_url(getenv('OXID_DB_URL'), PHP_URL_SCHEME),
                $currentDbConfig->getUser(),
                $currentDbConfig->getPass(),
                $currentDbConfig->getHost(),
                $currentDbConfig->getPort(),
                $nonExistentDb
            )
        );

        $this->get(SetupDbConnectionValidatorInterface::class)
            ->validate($configWithNonexistentDatabase);
    }

    public function testValidateWithCurrentConnection(): void
    {
        $dbConfig = new DatabaseConfiguration(getenv('OXID_DB_URL'));

        $this->expectException(DatabaseNotEmptyException::class);

        $this->get(SetupDbConnectionValidatorInterface::class)->validate($dbConfig);
    }
}
