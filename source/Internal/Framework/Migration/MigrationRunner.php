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

readonly class MigrationRunner implements MigrationRunnerInterface
{
    public function __construct(private ConnectionFactoryInterface $connectionFactory)
    {
    }

    public function run(string $migrationConfigPath, array $options = [], ?OutputInterface $output = null): int
    {
        $dependencyFactory = DependencyFactory::fromConnection(
            new YamlFile($migrationConfigPath),
            new ExistingConnection($this->connectionFactory->create())
        );

        $input = new ArrayInput($options + ['--allow-no-migration' => true]);
        $input->setInteractive(false);

        return (new DoctrineMigrateCommand($dependencyFactory))->run($input, $output ?? new ConsoleOutput());
    }
}
