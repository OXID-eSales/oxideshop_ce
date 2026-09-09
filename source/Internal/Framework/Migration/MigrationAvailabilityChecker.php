<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Doctrine\Migrations\Configuration\Migration\YamlFile;
use Symfony\Component\Filesystem\Path;

readonly class MigrationAvailabilityChecker implements MigrationAvailabilityCheckerInterface
{
    public function hasMigrationDirectories(string $migrationConfigPath): bool
    {
        $configDirectory = Path::getDirectory($migrationConfigPath);
        $migrationDirectories = (new YamlFile($migrationConfigPath))->getConfiguration()->getMigrationDirectories();

        foreach ($migrationDirectories as $migrationDirectory) {
            if (!is_dir(Path::makeAbsolute($migrationDirectory, $configDirectory))) {
                return false;
            }
        }

        return true;
    }
}
