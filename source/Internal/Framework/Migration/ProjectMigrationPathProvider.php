<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Edition\Edition;
use OxidEsales\EshopCommunity\Internal\Framework\Edition\EditionDirectoriesLocator;
use Symfony\Component\Filesystem\Path;

readonly class ProjectMigrationPathProvider implements MigrationPathProviderInterface
{
    public function __construct(private EditionDirectoriesLocator $editionDirectoriesLocator)
    {
    }

    public function getMigrationConfigPath(): string
    {
        return Path::join(
            $this->editionDirectoriesLocator->getEditionSourcePath(Edition::Community),
            'migration',
            'project_migrations.yml',
        );
    }
}
