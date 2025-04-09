<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\DataObject;

use Doctrine\DBAL\Tools\DsnParser;
use OxidEsales\EshopCommunity\Internal\Framework\Database\Configuration\InvalidDatabaseConfigurationException;

class DatabaseConfiguration
{
    private array $urlComponents;

    /**
     * List of URL schemes from a database URL and their mappings to driver.
     * @see \Doctrine\DBAL\DriverManager::$driverSchemeAliases
     */
    private static array $driverSchemeAliases = [
        'db2' => 'ibm_db2',
        'mssql' => 'pdo_sqlsrv',
        'mysql' => 'pdo_mysql',
        'mysql2' => 'pdo_mysql', // Amazon RDS, for some weird reason
        'postgres' => 'pdo_pgsql',
        'postgresql' => 'pdo_pgsql',
        'pgsql' => 'pdo_pgsql',
        'sqlite' => 'pdo_sqlite',
        'sqlite3' => 'pdo_sqlite',
    ];

    public function __construct(private readonly string $databaseUrl)
    {
        $this->urlComponents = (new DsnParser(self::$driverSchemeAliases))->parse($databaseUrl);
        $this->validateRequiredUrlComponents();
    }

    public function getDriver(): string
    {
        return $this->urlComponents['driver'];
    }

    public function getDatabaseUrl(): string
    {
        return $this->databaseUrl;
    }

    public function getUser(): string
    {
        return $this->urlComponents['user'] ?? '';
    }

    public function getPass(): string
    {
        return $this->urlComponents['password'] ?? '';
    }

    public function getHost(): string
    {
        return $this->urlComponents['host'];
    }

    public function getPort(): int
    {
        return $this->urlComponents['port'] ?? 3306;
    }

    public function getName(): string
    {
        return $this->urlComponents['dbname'] ?? '';
    }

    public function getOptions(): array
    {
        return $this->urlComponents['driverOptions'] ?? [];
    }

    public function getCharset(): ?string
    {
        return $this->urlComponents['charset'] ?? null;
    }

    public function isSocketConnection(): bool
    {
        return isset($this->urlComponents['socket']);
    }

    public function getSocket(): string
    {
        return trim($this->urlComponents['socket'], '()');
    }

    public function getConnectionParameters(): array
    {
        return $this->urlComponents;
    }

    private function validateRequiredUrlComponents(): void
    {
        if (
            empty($this->urlComponents['host']) ||
            !isset($this->urlComponents['driver']) ||
            !in_array($this->urlComponents['driver'], self::$driverSchemeAliases, true)
        ) {
            throw new InvalidDatabaseConfigurationException('Provided database URL is not valid');
        }
    }
}
