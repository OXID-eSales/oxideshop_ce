<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject\DatabaseConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Configuration\InvalidDatabaseConfigurationException;
use PHPUnit\Framework\TestCase;

final class DatabaseConfigurationTest extends TestCase
{
    public function testGetParameters(): void
    {
        $scheme = 'mysql';
        $driver = 'pdo_mysql';
        $username = uniqid('user-', true);
        $password = uniqid('secret-', true);
        $server = uniqid('server-', true);
        $port = 1234;
        $database = uniqid('db-', true);
        $encoding = 'utf8mb4';
        $driverOptions = '"SET @@SESSION.sql_mode=\"\""';
        $url = sprintf(
            '%s://%s:%s@%s:%d/%s?charset=%s&driverOptions[1002]=%s',
            $scheme,
            $username,
            $password,
            $server,
            $port,
            $database,
            $encoding,
            $driverOptions,
        );

        $databaseConfiguration = new DatabaseConfiguration($url);

        $this->assertEquals($url, $databaseConfiguration->getDatabaseUrl());
        $this->assertFalse($databaseConfiguration->isSocketConnection());
        $this->assertEquals($driver, $databaseConfiguration->getDriver());
        $this->assertEquals($username, $databaseConfiguration->getUser());
        $this->assertEquals($password, $databaseConfiguration->getPass());
        $this->assertEquals($server, $databaseConfiguration->getHost());
        $this->assertEquals($port, $databaseConfiguration->getPort());
        $this->assertEquals($database, $databaseConfiguration->getName());
        $this->assertEquals($encoding, $databaseConfiguration->getOptions()['charset']);
        $this->assertEquals($driverOptions, $databaseConfiguration->getOptions()['driverOptions'][1002]);
    }

    public function testGetParametersWithMinimalUrl(): void
    {
        $defaultPort = 3306;
        $scheme = 'sqlite';
        $driver = 'pdo_sqlite';
        $username = uniqid('user-', true);
        $server = uniqid('server-', true);
        $url = sprintf(
            '%s://%s@%s',
            $scheme,
            $username,
            $server
        );

        $databaseConfiguration = new DatabaseConfiguration($url);

        $this->assertEquals($url, $databaseConfiguration->getDatabaseUrl());
        $this->assertFalse($databaseConfiguration->isSocketConnection());
        $this->assertEquals($driver, $databaseConfiguration->getDriver());
        $this->assertEquals($username, $databaseConfiguration->getUser());
        $this->assertEmpty($databaseConfiguration->getPass());
        $this->assertEquals($server, $databaseConfiguration->getHost());
        $this->assertEquals($defaultPort, $databaseConfiguration->getPort());
    }

    public function testWithInvalidUrl(): void
    {
        $url = '123';

        $this->expectException(InvalidDatabaseConfigurationException::class);

        new DatabaseConfiguration($url);
    }

    public function testWithInvalidScheme(): void
    {
        $url = 'abc://def';

        $this->expectException(InvalidDatabaseConfigurationException::class);

        new DatabaseConfiguration($url);
    }

    public function testWithUnresolvableHost(): void
    {
        $url = 'mysql:abc';

        $this->expectException(InvalidDatabaseConfigurationException::class);

        new DatabaseConfiguration($url);
    }

    public function testWithUnresolvableUser(): void
    {
        $url = 'mysql://abc';

        $databaseConfiguration = new DatabaseConfiguration($url);

        $this->assertEmpty($databaseConfiguration->getUser());
    }

    public function testWithSocketConnection(): void
    {
        $username = uniqid('user-', true);
        $server = 'localhost';
        $socket = '/tmp/mysql.sock';
        $url = sprintf(
            'mysql://%s@%s?socket=(%s)',
            $username,
            $server,
            $socket,
        );

        $databaseConfiguration = new DatabaseConfiguration($url);

        $this->assertTrue($databaseConfiguration->isSocketConnection());
        $this->assertEquals($socket, $databaseConfiguration->getSocket());
    }

    public function testWithUrlEncodedSocketConnection(): void
    {
        $socket = '/tmp/mysql.sock';
        $socketEncoded = urlencode($socket);
        $url = sprintf(
            'mysql://root@localhost?socket=%s',
            $socketEncoded,
        );

        $databaseConfiguration = new DatabaseConfiguration($url);

        $this->assertEquals($socket, $databaseConfiguration->getSocket());
    }
}
