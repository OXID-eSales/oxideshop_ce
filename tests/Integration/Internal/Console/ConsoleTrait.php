<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Console;

use OxidEsales\EshopCommunity\Internal\Console\CommandsCollectionBuilder;
use OxidEsales\EshopCommunity\Internal\Console\Executor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @internal
 */
trait ConsoleTrait
{
    /**
     * @param Application $application
     * @param CommandsCollectionBuilder $commandsCollectionBuilder
     * @param $input
     * @return string
     */
    protected function execute(Application $application, CommandsCollectionBuilder $commandsCollectionBuilder, $input): string
    {
        $executor = new Executor($application, $commandsCollectionBuilder);

        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $executor->execute($input, $output);
        $stream = $output->getStream();
        rewind($stream);
        $display = stream_get_contents($stream);

        return $display;
    }
}
