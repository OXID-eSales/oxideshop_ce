<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration;

use Doctrine\Migrations\Configuration\Connection\ExistingConnection;
use Doctrine\Migrations\Configuration\Migration\YamlFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\Migrations\Tools\Console\Command\MigrateCommand as DoctrineMigrateCommand;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

readonly class TaggedMigrationExecutor implements ConfigurableMigrationExecutorInterface
{
    /** @param iterable<MigrationPathProviderInterface> $providers */
    public function __construct(
        private ConnectionFactoryInterface $connectionFactory,
        private iterable $providers,
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

            if (!is_file($configPath)) {
                continue;
            }

            $connectionLoader ??= new ExistingConnection($this->connectionFactory->create());
            $status = $this->exitCodeResolver->combine($status, $this->runMigrations($configPath, $connectionLoader, $options, $output));
        }

        return $status;
    }

    /**
     * @param array<string, mixed> $options
     */
    private function runMigrations(string $configPath, ExistingConnection $connectionLoader, array $options, ?OutputInterface $output): int
    {
        $dependencyFactory = DependencyFactory::fromConnection(
            new YamlFile($configPath),
            $connectionLoader
        );

        $input = new ArrayInput($options + ['--allow-no-migration' => true]);
        $input->setInteractive(false);

        return (new DoctrineMigrateCommand($dependencyFactory))->run($input, $output ?? new ConsoleOutput());
    }
}
