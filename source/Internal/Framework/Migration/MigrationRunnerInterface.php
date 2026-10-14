<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Symfony\Component\Console\Output\OutputInterface;

interface MigrationRunnerInterface
{
    /**
     * @param array<string, mixed> $options
     */
    public function run(string $migrationConfigPath, array $options = [], ?OutputInterface $output = null): int;
}
