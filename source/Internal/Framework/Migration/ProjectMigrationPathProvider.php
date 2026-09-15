<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Symfony\Component\Filesystem\Path;

readonly class ProjectMigrationPathProvider implements MigrationPathProviderInterface
{
    public function __construct(private BasicContextInterface $context)
    {
    }

    public function getMigrationConfigPath(): string
    {
        return Path::join($this->context->getSourcePath(), 'migration', 'project_migrations.yml');
    }
}
