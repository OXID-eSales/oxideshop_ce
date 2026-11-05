<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use DateTimeImmutable;
use Symfony\Component\Filesystem\Path;

readonly class MigrationSqlFilePathResolver implements MigrationSqlFilePathResolverInterface
{
    public function resolve(string $path): string
    {
        $absolutePath = Path::makeAbsolute($path, (string) getcwd());

        if (!is_dir($absolutePath)) {
            return $absolutePath;
        }

        return Path::join($absolutePath, $this->getGeneratedFileName());
    }

    private function getGeneratedFileName(): string
    {
        return sprintf('doctrine_migration_%s.sql', (new DateTimeImmutable())->format('YmdHis'));
    }
}
