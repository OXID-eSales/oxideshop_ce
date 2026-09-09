<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Symfony\Component\Console\Output\OutputInterface;

readonly class CompositeMigrationExecutor implements ConfigurableMigrationExecutorInterface
{
    public function __construct(
        private iterable $executors,
        private MigrationExitCodeResolverInterface $exitCodeResolver,
    ) {
    }

    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int
    {
        $status = 0;

        foreach ($this->executors as $executor) {
            $exitCode = $executor->executeWithOptions($options, $output);
            $status = $this->exitCodeResolver->combine($status, $exitCode);
        }

        return $status;
    }
}
