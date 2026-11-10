<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Doctrine\Migrations\Generator\ConcatenationFileBuilder;
use Doctrine\Migrations\Query\Query;
use Doctrine\Migrations\QueryWriter;
use Symfony\Component\Filesystem\Filesystem;

readonly class MigrationSqlFileWriter implements QueryWriter
{
    public function __construct(private Filesystem $filesystem)
    {
    }

    /**
     * @param array<string, Query[]> $queriesByVersion
     */
    public function write(string $path, string $direction, array $queriesByVersion): bool
    {
        $this->filesystem->appendToFile(
            $path,
            (new ConcatenationFileBuilder())->buildMigrationFile($queriesByVersion, $direction)
        );

        return true;
    }
}
