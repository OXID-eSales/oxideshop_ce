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
     * @var ConsoleOutput
     */
    private $consoleOutput;

    /**
     * @var ShopAdapterInterface
     */
    private $shopAdapter;

    /**
     * @param Application               $application
     * @param ConsoleOutputInterface    $consoleOutput
     * @param CommandsCollectionBuilder $commandsCollectionBuilder
     * @param ShopAdapterInterface      $shopAdapter
     */
    public function __construct(
        Application $application,
        ConsoleOutputInterface $consoleOutput,
        CommandsCollectionBuilder $commandsCollectionBuilder,
        ShopAdapterInterface $shopAdapter
    ) {
        $this->application = $application;
        $this->consoleOutput = $consoleOutput;
        $this->commandsCollectionBuilder = $commandsCollectionBuilder;
        $this->shopAdapter = $shopAdapter;
    }

    /**
     * @inheritdoc
     */
    public function execute(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $input) {
            $input = new ArgvInput();
        }
        if (null === $output) {
            $output = new ConsoleOutput();
        }

        $shopId = (int) $input->getParameterOption('--shop-id', 1);
        $shopId = $shopId === 0 ? 1 : $shopId;
        try {
            $this->shopAdapter->switchToShop($shopId);
            foreach ($this->commandsCollectionBuilder->build()->toArray() as $command) {
                $this->application->add($command);
            }
            $this->application->run($input, $output);
        } catch (ShopSwitchException $shopSwitchException) {
            $output->writeln('<error>'.$shopSwitchException->getMessage().'</error>');
            if ($this->application->isAutoExitEnabled()) {
                exit(1);
            }
        }
    }
}
