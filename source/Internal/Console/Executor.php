<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Console;

use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapterInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
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
     * @inheritdoc
     */
    public function execute(InputInterface $input = null, OutputInterface $output = null)
    {
        foreach ($this->commandsCollectionBuilder->build()->toArray() as $command) {
            $this->application->add($command);
        }
        $this->application->run($input, $output);
    }
}
