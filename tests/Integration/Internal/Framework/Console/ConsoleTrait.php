<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Console;

use OxidEsales\EshopCommunity\Internal\Framework\Console\CommandsProvider\CommandsProviderInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Console\Executor;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Output\StreamOutput;

/**
 * @internal
 */
trait ConsoleTrait
{
    /**
     * @param Application $application
     * @param CommandsProviderInterface $commandsProvider
     * @param $input
     * @return string
     */
    protected function execute(Application $application, CommandsProviderInterface $commandsProvider, $input): string
    {
        $executor = new Executor($application, $commandsProvider);

        $output = new StreamOutput(fopen('php://memory', 'w', false));
        $executor->execute($input, $output);
        $stream = $output->getStream();
        rewind($stream);
        $display = stream_get_contents($stream);

        return $display;
    }
}
