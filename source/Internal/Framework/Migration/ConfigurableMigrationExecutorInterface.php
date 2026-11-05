<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlFileNotWritableException;
use Symfony\Component\Console\Output\OutputInterface;

interface ConfigurableMigrationExecutorInterface
{
    /**
     * @param array<string, mixed> $options
     * @throws MigrationSqlFileNotWritableException
     */
    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int;
}
