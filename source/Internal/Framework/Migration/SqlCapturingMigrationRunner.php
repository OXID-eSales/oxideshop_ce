<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Symfony\Component\Console\Output\OutputInterface;

readonly class SqlCapturingMigrationRunner implements MigrationRunnerInterface
{
    public function __construct(
        private MigrationRunnerInterface $runner,
        private MigrationSqlOutputInterface $sqlOutput,
    ) {
    }

    public function run(string $migrationConfigPath, array $options = [], ?OutputInterface $output = null): int
    {
        return $this->sqlOutput->capture(
            $options,
            fn(array $redirectedOptions): int => $this->runner->run($migrationConfigPath, $redirectedOptions, $output)
        );
    }
}
