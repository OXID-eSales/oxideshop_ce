<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

readonly class AccumulatingSqlMigrationRunner implements MigrationRunnerInterface
{
    private const FAILURE = 1;

    public function __construct(
        private MigrationRunnerInterface $runner,
        private Filesystem $filesystem,
    ) {
    }

    public function run(string $migrationConfigPath, array $options = [], ?OutputInterface $output = null): int
    {
        $targetFile = $options['--write-sql'] ?? null;
        if (!is_string($targetFile)) {
            return $this->runner->run($migrationConfigPath, $options, $output);
        }

        $temporaryFile = $this->filesystem->tempnam(sys_get_temp_dir(), 'oxid_migration_sql_');
        try {
            $exitCode = $this->runner->run(
                $migrationConfigPath,
                array_merge($options, ['--write-sql' => $temporaryFile]),
                $output
            );

            return $this->append($targetFile, (string) file_get_contents($temporaryFile)) ? $exitCode : self::FAILURE;
        } finally {
            $this->filesystem->remove($temporaryFile);
        }
    }

    private function append(string $targetFile, string $sql): bool
    {
        try {
            $this->filesystem->appendToFile($targetFile, $sql);
        } catch (IOException) {
            return false;
        }

        return true;
    }
}
