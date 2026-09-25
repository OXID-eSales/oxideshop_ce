<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Symfony\Component\Console\Output\OutputInterface;

readonly class TaggedMigrationExecutor implements ConfigurableMigrationExecutorInterface
{
    /** @param iterable<MigrationPathProviderInterface> $providers */
    public function __construct(
        private iterable $providers,
        private MigrationAvailabilityCheckerInterface $availabilityChecker,
        private MigrationRunnerInterface $migrationRunner,
        private MigrationExitCodeResolverInterface $exitCodeResolver,
    ) {
    }

    /**
     * @param array<string, mixed> $options
     */
    public function executeWithOptions(array $options = [], ?OutputInterface $output = null): int
    {
        $status = 0;

        foreach ($this->providers as $provider) {
            $configPath = $provider->getMigrationConfigPath();

            if (!$this->isExecutable($provider, $configPath)) {
                continue;
            }

            $exitCode = $this->migrationRunner->run($configPath, $options, $output);
            $status = $this->exitCodeResolver->combine($status, $exitCode);
        }

        return $status;
    }

    private function isExecutable(MigrationPathProviderInterface $provider, string $configPath): bool
    {
        return $provider instanceof OptionalMigrationPathProviderInterface
            ? $this->availabilityChecker->hasMigrations($configPath)
            : is_file($configPath);
    }
}
