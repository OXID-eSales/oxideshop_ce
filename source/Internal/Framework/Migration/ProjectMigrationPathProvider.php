<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\Exception\MigrationsNotFoundException;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

/**
 * @deprecated
 */
readonly class ProjectMigrationPathProvider implements MigrationPathProviderInterface
{
    public function __construct(
        private BasicContextInterface $context,
        private MigrationAvailabilityCheckerInterface $availabilityChecker,
    ) {
    }

    public function getMigrationConfigPath(): string
    {
        $migrationConfigPath = Path::join($this->context->getSourcePath(), 'migration', 'project_migrations.yml');

        if (!$this->availabilityChecker->hasMigrations($migrationConfigPath)) {
            throw new MigrationsNotFoundException();
        }

        return $migrationConfigPath;
    }
}
