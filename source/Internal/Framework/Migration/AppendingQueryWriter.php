<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\Migrations\Query\Query;
use Doctrine\Migrations\QueryWriter;

readonly class AppendingQueryWriter implements QueryWriter
{
    /** @param array<string, Query[]> $queriesByVersion */
    public function write(string $path, string $direction, array $queriesByVersion): bool
    {
        $now = new DateTimeImmutable();
        $sql = sprintf("-- Doctrine Migration File Generated on %s\n", $now->format('Y-m-d H:i:s'));

        foreach ($queriesByVersion as $version => $queries) {
            $sql .= "\n-- Version " . $version . "\n";
            foreach ($queries as $query) {
                $sql .= $query->getStatement() . ";\n";
            }
        }

        return file_put_contents($path, $sql, FILE_APPEND) !== false;
    }
}
