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
        $driver = 'mysql';
        $username = uniqid('user-', true);
        $password = uniqid('secret-', true);
        $server = uniqid('server-', true);
        $port = 1234;
        $database = uniqid('db-', true);
        $encoding = 'utf8mb4';
        $driverOptions = '"SET @@SESSION.sql_mode=\"\""';
        $url = sprintf(
            '%s://%s:%s@%s:%d/%s?charset=%s&driverOptions[1002]=%s',
            $driver,
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
        $this->assertEquals($username, $databaseConfiguration->getUsername());
        $this->assertEquals($password, $databaseConfiguration->getPassword());
        $this->assertEquals($server, $databaseConfiguration->getServer());
        $this->assertEquals($port, $databaseConfiguration->getPort());
        $this->assertEquals($database, $databaseConfiguration->getDatabase());
        $this->assertEquals($encoding, $databaseConfiguration->getOptions()['charset']);
        $this->assertEquals($driverOptions, $databaseConfiguration->getOptions()['driverOptions'][1002]);
    }

    public function testGetParametersWithMinimalUrl(): void
    {
        $driver = 'mysql';
        $username = uniqid('user-', true);
        $password = uniqid('secret-', true);
        $server = uniqid('server-', true);
        $url = sprintf(
            '%s://%s:%s@%s',
            $driver,
            $username,
            $password,
            $server
        );

        $databaseConfiguration = new DatabaseConfiguration($url);

        $this->assertEquals($url, $databaseConfiguration->getDatabaseUrl());
        $this->assertFalse($databaseConfiguration->isSocketConnection());
        $this->assertEquals($driver, $databaseConfiguration->getDriver());
        $this->assertEquals($username, $databaseConfiguration->getUsername());
        $this->assertEquals($password, $databaseConfiguration->getPassword());
        $this->assertEquals($server, $databaseConfiguration->getServer());
    }

    public function testWithInvalidUrl(): void
    {
        $url = '123';

        $this->expectException(InvalidDatabaseConfigurationException::class);

        new DatabaseConfiguration($url);
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
