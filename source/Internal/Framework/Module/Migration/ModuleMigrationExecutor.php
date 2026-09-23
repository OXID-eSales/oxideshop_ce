<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Migration;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationAvailabilityCheckerInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationRunnerInterface;
use Symfony\Component\Console\Output\OutputInterface;

readonly class ModuleMigrationExecutor implements ConfigurableMigrationExecutorInterface
{
    public function __construct(
        private ModuleMigrationConfigLocatorInterface $configLocator,
        private MigrationAvailabilityCheckerInterface $availabilityChecker,
        private MigrationRunnerInterface $migrationRunner,
        private MigrationExitCodeResolverInterface $exitCodeResolver,
    ) {
    }

    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int
    {
        $status = 0;

        foreach ($this->configLocator->getMigrationConfigPaths() as $configPath) {
            if (!$this->availabilityChecker->hasMigrationDirectories($configPath)) {
                continue;
            }

            $exitCode = $this->migrationRunner->run($configPath, $options, $output);
            $status = $this->exitCodeResolver->combine($status, $exitCode);
        }

        return $status;
    }
}
