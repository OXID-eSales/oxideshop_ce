<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Symfony\Component\Console\Output\OutputInterface;

readonly class WriteSqlMigrationExecutor implements ConfigurableMigrationExecutorInterface
{
    private const WRITE_SQL_OPTION = '--write-sql';
    private const CURRENT_DIRECTORY = '.';

    public function __construct(
        private ConfigurableMigrationExecutorInterface $migrationExecutor,
        private MigrationSqlFilePathResolverInterface $sqlFilePathResolver,
        private MigrationSqlFileWriterInterface $sqlFileWriter,
    ) {
    }

    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int
    {
        if (!$this->isSqlFileRequested($options)) {
            return $this->migrationExecutor->executeWithOptions($options, $output);
        }

        $options[self::WRITE_SQL_OPTION] = $this->prepareSqlFile(
            $options[self::WRITE_SQL_OPTION] ?? self::CURRENT_DIRECTORY
        );

        return $this->migrationExecutor->executeWithOptions($options, $output);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function isSqlFileRequested(array $options): bool
    {
        if (!array_key_exists(self::WRITE_SQL_OPTION, $options)) {
            return false;
        }

        $path = $options[self::WRITE_SQL_OPTION];

        return $path === null || is_string($path);
    }

    private function prepareSqlFile(string $path): string
    {
        $filePath = $this->sqlFilePathResolver->resolve($path);
        $this->sqlFileWriter->clear($filePath);

        return $filePath;
    }
}
