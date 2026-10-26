<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlOutputException;

interface MigrationSqlOutputInterface
{
    /**
     * @throws MigrationSqlOutputException
     */
    public function collect(array $options, callable $migrations): int;

    /**
     * @throws MigrationSqlOutputException
     */
    public function capture(array $options, callable $migration): int;
}
