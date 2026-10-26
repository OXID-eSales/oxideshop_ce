<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlOutputException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

readonly class SqlCollectingMigrationExecutor implements ConfigurableMigrationExecutorInterface
{
    public function __construct(
        private ConfigurableMigrationExecutorInterface $executor,
        private MigrationSqlOutputInterface $sqlOutput,
    ) {
    }

    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int
    {
        try {
            return $this->sqlOutput->collect(
                $options,
                fn(array $redirectedOptions): int => $this->executor->executeWithOptions($redirectedOptions, $output)
            );
        } catch (MigrationSqlOutputException $exception) {
            $output?->writeln('Failed to write the migration SQL output file: ' . $exception->getMessage());

            return Command::FAILURE;
        }
    }
}
