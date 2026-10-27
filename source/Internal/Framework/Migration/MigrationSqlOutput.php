<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use DateTimeImmutable;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationSqlOutputException;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

class MigrationSqlOutput implements MigrationSqlOutputInterface
{
    private const OPTION = '--write-sql';

    private ?string $file = null;

    public function __construct(private readonly Filesystem $filesystem)
    {
    }

    public function collect(array $options, callable $migrations): int
    {
        if ($this->isCollecting() || !$this->isRequested($options)) {
            return $migrations($options);
        }

        $this->startCollecting($this->resolveFile($options[self::OPTION]));

        try {
            return $migrations(array_merge($options, [self::OPTION => $this->file]));
        } finally {
            $this->stopCollecting();
        }
    }

    public function capture(array $options, callable $migration): int
    {
        if (!$this->isCollecting() || !$this->isRequested($options)) {
            return $migration($options);
        }

        $temporaryFile = $this->filesystem->tempnam(sys_get_temp_dir(), 'oxid_migration_sql_');
        try {
            $exitCode = $migration(array_merge($options, [self::OPTION => $temporaryFile]));
            $this->append($this->read($temporaryFile));

            return $exitCode;
        } finally {
            $this->filesystem->remove($temporaryFile);
        }
    }

    /**
     * @throws MigrationSqlOutputException
     */
    private function startCollecting(string $file): void
    {
        $this->truncate($file);
        $this->file = $file;
    }

    private function stopCollecting(): void
    {
        $this->file = null;
    }

    private function isCollecting(): bool
    {
        return $this->file !== null;
    }

    private function isRequested(array $options): bool
    {
        return array_key_exists(self::OPTION, $options) && $options[self::OPTION] !== false;
    }

    private function resolveFile(?string $path): string
    {
        $path ??= $this->workingDirectory();

        if (!is_dir($path)) {
            return $path;
        }

        return Path::join($path, $this->generatedFileName());
    }

    private function workingDirectory(): string
    {
        return (string) getcwd();
    }

    private function generatedFileName(): string
    {
        return 'doctrine_migration_' . (new DateTimeImmutable())->format('YmdHis') . '.sql';
    }

    /**
     * @throws MigrationSqlOutputException
     */
    private function truncate(string $file): void
    {
        $this->write(fn() => $this->filesystem->dumpFile($file, ''));
    }

    /**
     * @throws MigrationSqlOutputException
     */
    private function read(string $file): string
    {
        $sql = is_file($file) ? file_get_contents($file) : false;
        if ($sql === false) {
            throw new MigrationSqlOutputException(sprintf('Failed to read "%s".', $file));
        }

        return $sql;
    }

    /**
     * @throws MigrationSqlOutputException
     */
    private function append(string $sql): void
    {
        $this->write(fn() => $this->filesystem->appendToFile((string) $this->file, $sql));
    }

    /**
     * @throws MigrationSqlOutputException
     */
    private function write(callable $operation): void
    {
        try {
            $operation();
        } catch (IOException $exception) {
            throw new MigrationSqlOutputException($exception->getMessage(), 0, $exception);
        }
    }
}
