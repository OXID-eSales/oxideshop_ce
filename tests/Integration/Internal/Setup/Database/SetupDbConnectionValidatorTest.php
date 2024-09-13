<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Setup\Database;

use Doctrine\DBAL\Exception\ConnectionException;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Setup\Database\DatabaseAlreadyExistsException;
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
        $dbConfig->getHost()->willReturn(uniqid('db-server-', true));
        $dbConfig->getPort()->willReturn(3306);
        $dbConfig->getScheme()->willReturn('mysql');
        $dbConfig->getDatabaseUrl()->willReturn(Argument::type('string'));

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
                $currentDbConfig->getScheme(),
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

        $this->expectException(DatabaseAlreadyExistsException::class);

        $this->get(SetupDbConnectionValidatorInterface::class)->validate($dbConfig);
    }
}
