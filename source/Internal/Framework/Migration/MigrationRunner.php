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
use Doctrine\Migrations\Tools\Console\ConsoleLogger;
use OxidEsales\EshopCommunity\Internal\Framework\Database\ConnectionFactoryInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

readonly class MigrationRunner implements MigrationRunnerInterface
{
    public function __construct(private ConnectionFactoryInterface $connectionFactory)
    {
    }

    public function run(string $migrationConfigPath, array $options = [], ?OutputInterface $output = null): int
    {
        $output ??= new ConsoleOutput();
        $dependencyFactory = $this->createDependencyFactory($migrationConfigPath, $output);

        if (!$this->hasRegisteredMigrations($dependencyFactory)) {
            return DoctrineMigrateCommand::SUCCESS;
        }

        $input = new ArrayInput($options);
        $input->setInteractive(false);

        return (new DoctrineMigrateCommand($dependencyFactory))->run($input, $output);
    }

    private function createDependencyFactory(string $migrationConfigPath, OutputInterface $output): DependencyFactory
    {
        return DependencyFactory::fromConnection(
            new YamlFile($migrationConfigPath),
            new ExistingConnection($this->connectionFactory->create()),
            new ConsoleLogger($output)
        );
    }

    private function hasRegisteredMigrations(DependencyFactory $dependencyFactory): bool
    {
        return count($dependencyFactory->getMigrationRepository()->getMigrations()) > 0;
    }
}
