<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

readonly class CompositeMigrationExecutor implements MigrationExecutorInterface
{
    public function __construct(
        private ConfigurableMigrationExecutorInterface $migrationExecutor,
        private ConfigurableMigrationExecutorInterface $taggedMigrationExecutor,
        private MigrationExitCodeResolverInterface $exitCodeResolver,
    ) {
    }

    public function execute(): void
    {
        $migrationStatus = $this->migrationExecutor->executeWithOptions();
        $taggedMigrationStatus = $this->taggedMigrationExecutor->executeWithOptions();
        $status = $this->exitCodeResolver->combine($migrationStatus, $taggedMigrationStatus);

        if ($status !== 0) {
            throw new MigrationExecutionFailedException($status);
        }
    }
}
