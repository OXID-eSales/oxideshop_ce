<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Migration\Command;

use OxidEsales\EshopCommunity\Internal\Framework\Migration\ConfigurableMigrationExecutorInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationExitCodeResolverInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Migration\MigrationOptionsForwarderInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class DatabaseMigrateCommand extends Command
{
    public function __construct(
        private readonly ConfigurableMigrationExecutorInterface $migrationExecutor,
        private readonly ConfigurableMigrationExecutorInterface $taggedMigrationExecutor,
        private readonly MigrationOptionsForwarderInterface $optionsForwarder,
        private readonly MigrationExitCodeResolverInterface $exitCodeResolver,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Applies pending database migrations');
        $this->optionsForwarder->mirror($this->getDefinition());
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $options = $this->optionsForwarder->collect($input);

        $status = $this->migrationExecutor->executeWithOptions($options, $output);

        return $this->exitCodeResolver->combine($status, $this->taggedMigrationExecutor->executeWithOptions($options, $output));
    }
}
