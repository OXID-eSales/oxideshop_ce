<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Configuration\DataObject;

class SystemConfiguration
{
    private string $databaseUrl;

    public function getDatabaseUrl(): string
    {
        return $this->databaseUrl;
    }

    public function setDatabaseUrl(string $databaseUrl): void
    {
        $this->databaseUrl = $databaseUrl;
    }
}
