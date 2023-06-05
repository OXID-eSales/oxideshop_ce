<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Console;

use OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider\CommandsProviderInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @inheritdoc
 * @deprecated since v7.1.0
 */
class Executor implements ExecutorInterface
{
    public const SHOP_ID_PARAMETER_OPTION_NAME = 'shop-id';

    public function __construct(
        private Application $application,
        private CommandsProviderInterface $servicesCommandsProvider
    ) {
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
