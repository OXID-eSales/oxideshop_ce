<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlFileNotWritableException;

interface MigrationSqlFileWriterInterface
{
    /**
     * @throws MigrationSqlFileNotWritableException
     */
    public function clear(string $filePath): void;

    /**
     * @throws MigrationSqlFileNotWritableException
     */
    public function append(string $filePath, string $sql): void;
}
