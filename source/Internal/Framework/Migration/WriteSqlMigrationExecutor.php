<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use SplFileObject;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Path;

readonly class WriteSqlMigrationExecutor implements ConfigurableMigrationExecutorInterface
{
    private const WRITE_SQL_OPTION = '--write-sql';

    public function __construct(private ConfigurableMigrationExecutorInterface $migrationExecutor)
    {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int
    {
        if ($this->isSqlFileRequested($options)) {
            $sqlFile = $this->createEmptySqlFile($options[self::WRITE_SQL_OPTION] ?? (string) getcwd());
            $output?->writeln(
                sprintf('Writing migration SQL to "%s"', $sqlFile),
                OutputInterface::VERBOSITY_VERBOSE
            );
            $options[self::WRITE_SQL_OPTION] = $sqlFile;
        }

        return $this->migrationExecutor->executeWithOptions($options, $output);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function isSqlFileRequested(array $options): bool
    {
        return array_key_exists(self::WRITE_SQL_OPTION, $options)
            && !is_bool($options[self::WRITE_SQL_OPTION]);
    }

    private function createEmptySqlFile(string $path): string
    {
        $sqlFile = $this->resolveSqlFilePath($path);
        new SplFileObject($sqlFile, 'w');

        return $sqlFile;
    }

    private function resolveSqlFilePath(string $path): string
    {
        $absolutePath = Path::makeAbsolute($path, (string) getcwd());

        if (!is_dir($absolutePath)) {
            return $absolutePath;
        }

        return Path::join($absolutePath, sprintf('doctrine_migration_%s.sql', date('YmdHis')));
    }
}
