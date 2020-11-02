<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Console;

use OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider\CommandsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider\ServicesCommandsProvider;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * {@inheritdoc}
 */
class Executor implements ExecutorInterface
{
    public const SHOP_ID_PARAMETER_OPTION_NAME = 'shop-id';

    /**
     * @var Application
     */
    private $application;

    /**
     * @var ServicesCommandsProvider
     */
    private $servicesCommandsProvider;

    public function __construct(
        Application $application,
        CommandsProviderInterface $commandsProvider
    ) {
        $this->application = $application;
        $this->servicesCommandsProvider = $commandsProvider;
    }

    /**
     * Executes commands.
     */
    public function execute(InputInterface $input = null, OutputInterface $output = null): void
    {
        $this->application->addCommands($this->servicesCommandsProvider->getCommands());
        $this->application->run($input, $output);
    }
}
