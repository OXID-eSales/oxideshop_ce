<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use DateTimeImmutable;
use Symfony\Component\Filesystem\Exception\IOException;
use Symfony\Component\Filesystem\Filesystem;

readonly class MigrationSqlOutput implements MigrationSqlOutputInterface
{
    public function __construct(private Filesystem $filesystem)
    {
    }

    public function resolveFile(array $options): ?string
    {
        if (!array_key_exists('--write-sql', $options) || $options['--write-sql'] === false) {
            return null;
        }

        $path = $options['--write-sql'] ?? (string) getcwd();

        return is_dir($path)
            ? rtrim((string) realpath($path), '/') . '/doctrine_migration_' . $this->timestamp() . '.sql'
            : $path;
    }

    public function prepare(string $file): bool
    {
        try {
            $this->filesystem->dumpFile($file, '');
        } catch (IOException) {
            return false;
        }

        return true;
    }

    private function timestamp(): string
    {
        return (new DateTimeImmutable())->format('YmdHis');
    }
}
