<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject;

class DatabaseConfiguration
{
    private string $driver;
    private string $charset;
    private string $host;
    private string $port;
    private string $name;
    private string $user;
    private string $password;
    private array $driverOptions;
    private string $unixSocket;

    public function getDriver(): string
    {
        return $this->driver;
    }

    public function setDriver(string $driver): void
    {
        $this->driver = $driver;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function setPort(string $port): void
    {
        $this->port = $port;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function getDriverOptions(): array
    {
        return $this->driverOptions;
    }

    public function setDriverOptions(array $driverOptions): void
    {
        $this->driverOptions = $driverOptions;
    }

    public function getUnixSocket(): string
    {
        return $this->unixSocket;
    }

    public function isUnixSocketConnection(): bool
    {
        return !empty($this->unixSocket);
    }

    public function setUnixSocket(string $unixSocket): void
    {
        $this->unixSocket = $unixSocket;
    }
}
