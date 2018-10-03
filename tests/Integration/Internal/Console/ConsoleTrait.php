<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Console;

use OxidEsales\EshopCommunity\Internal\Console\CommandsCollectionBuilder;
use OxidEsales\EshopCommunity\Internal\Console\Executor;
use OxidEsales\EshopCommunity\Internal\Adapter\ShopAdapter;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @internal
 */
trait ConsoleTrait
{
    /**
     * @param $commandsCollectionBuilder
     * @return string
     */
    protected function execute(CommandsCollectionBuilder $commandsCollectionBuilder, $input): string
    {
        $executor = new Executor($this->getConsoleApplication(), new ConsoleOutput(), $commandsCollectionBuilder, new ShopAdapter());

        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $executor->execute($input, $output);
        $stream = $output->getStream();
        rewind($stream);
        $display = stream_get_contents($stream);

        return $display;
    }

    /**
     * @return object
     */
    private function getConsoleApplication(): Application
    {
        $application = $this->get('symfony.component.console.application');
        $application->setAutoExit(false);
        return $application;
    }
}
