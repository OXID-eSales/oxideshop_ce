<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\Configuration\InvalidDatabaseConfigurationException;

use function parse_url;

class DatabaseConfiguration
{
    private array $urlComponents;

    public function __construct(private readonly string $databaseUrl)
    {
        $urlComponents = parse_url($this->databaseUrl);
        if (!$urlComponents || !isset($urlComponents['scheme'])) {
            throw new InvalidDatabaseConfigurationException("'$this->databaseUrl' is not a valid database URL");
        }
        $this->urlComponents = $urlComponents;
    }

    public function getDriver(): string
    {
        return $this->urlComponents['scheme'];
    }

    public function getDatabaseUrl(): string
    {
        return $this->databaseUrl;
    }

    public function getUsername(): string
    {
        return $this->urlComponents['user'];
    }

    public function getPassword(): string
    {
        return $this->urlComponents['pass'];
    }

    public function getServer(): string
    {
        return $this->urlComponents['host'];
    }

    public function getPort(): int
    {
        return $this->urlComponents['port'];
    }

    public function getDatabase(): string
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
