<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Symfony\Component\Console\Output\OutputInterface;

/**
 * @deprecated since v7.6.0, will be removed in v8.0, use
 *             ConfigurableMigrationExecutorInterface::executeWithOptions() instead
 */
readonly class MigrationExecutor implements MigrationExecutorInterface, ConfigurableMigrationExecutorInterface
{
    public function __construct(private ConfigurableMigrationExecutorInterface $migrationExecutor)
    {
    }

    public function execute(): void
    {
        $this->executeWithOptions();
    }

    /**
     * @param array<string, mixed> $options
     */
    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int
    {
        return $this->migrationExecutor->executeWithOptions($options, $output);
    }
}
