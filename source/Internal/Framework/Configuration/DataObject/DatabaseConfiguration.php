<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\InvalidDatabaseConfigurationException;

use function is_array;
use function parse_url;

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

    /**
     * @throws InvalidDatabaseConfigurationException
     */
    public function __construct(private readonly string $databaseUrl)
    {
        $urlComponents = parse_url($this->databaseUrl);
        if (
            !$urlComponents ||
            !is_array($urlComponents) ||
            !isset($urlComponents['scheme'], self::$driverSchemeAliases[$urlComponents['scheme']]) ||
            empty($urlComponents['host'])
        ) {
            throw new InvalidDatabaseConfigurationException("'$this->databaseUrl' is not a valid database URL");
        }
        $this->urlComponents = $urlComponents;
    }

    public function getScheme(): string
    {
        return $this->urlComponents['scheme'];
    }

    public function getDriver(): string
    {
        return self::$driverSchemeAliases[$this->urlComponents['scheme']];
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
        return $this->urlComponents['pass'] ?? '';
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
        return isset($this->urlComponents['path']) ?
            ltrim($this->urlComponents['path'], '/') :
            '';
    }

    public function getOptions(): array
    {
        if (!empty($this->urlComponents['query'])) {
            parse_str($this->urlComponents['query'], $options);
        }

        return $options ?? [];
    }

    public function isSocketConnection(): bool
    {
        return !empty($this->getOptions()['socket']);
    }

    public function getSocket(): string
    {
        return trim($this->getOptions()['socket'], '()');
    }
}
