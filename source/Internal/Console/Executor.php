<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @inheritdoc
 * @internal
 */
class Executor implements ExecutorInterface
{
    /**
     * @var Application
     */
    private $application;

    /**
     * @var CommandsCollectionBuilder
     */
    private $commandsCollectionBuilder;

    /**
     * @param Application               $application
     * @param CommandsCollectionBuilder $commandsCollectionBuilder
     */
    public function __construct(
        Application $application,
        CommandsCollectionBuilder $commandsCollectionBuilder
    ) {
        $this->application = $application;
        $this->commandsCollectionBuilder = $commandsCollectionBuilder;
    }

    /**
     * Executes commands.
     *
     * @param InputInterface|null  $input
     * @param OutputInterface|null $output
     */
    public function execute(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->application->addCommands($this->commandsCollectionBuilder->build()->toArray());
        $this->application->run($input, $output);
    }
}
