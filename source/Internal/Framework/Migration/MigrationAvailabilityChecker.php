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
    private const NON_MIGRATION_ENTRIES = ['.', '..', '.gitkeep'];

    public function hasMigrations(string $migrationConfigPath): bool
    {
        if (!is_file($migrationConfigPath)) {
            return false;
        }

        $configDirectory = Path::getDirectory($migrationConfigPath);
        $migrationDirectories = (new YamlFile($migrationConfigPath))->getConfiguration()->getMigrationDirectories();

        foreach ($migrationDirectories as $migrationDirectory) {
            if ($this->containsMigration(Path::makeAbsolute($migrationDirectory, $configDirectory))) {
                return true;
            }
        }

        return false;
    }

    private function containsMigration(string $directory): bool
    {
        return is_dir($directory)
            && !empty(array_diff(scandir($directory), self::NON_MIGRATION_ENTRIES));
    }
}
