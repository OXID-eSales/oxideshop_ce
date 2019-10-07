<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Console;

use OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider\CommandsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider\ServicesCommandsProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @inheritdoc
 */
class Executor implements ExecutorInterface
{
    const SHOP_ID_PARAMETER_OPTION_NAME = 'shop-id';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var ServicesCommandsProvider
     */
    private $servicesCommandsProvider;

    /**
     * @param Application               $application
     * @param CommandsProviderInterface $commandsProvider
     */
    public function __construct(
        Application $application,
        CommandsProviderInterface $commandsProvider
    ) {
        $this->application = $application;
        $this->servicesCommandsProvider = $commandsProvider;
    }

    /**
     * Executes commands.
     *
     * @param InputInterface|null  $input
     * @param OutputInterface|null $output
     */
    public function execute(InputInterface $input = null, OutputInterface $output = null)
    {
        $this->application->addCommands($this->servicesCommandsProvider->getCommands());
        $this->application->run($input, $output);
    }
}
